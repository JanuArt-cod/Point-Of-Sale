<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // 1. Menampilkan Daftar Riwayat Transaksi (Dilengkapi Filter Tanggal & Pencarian)
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'customer', 'voucher'])->latest();

        // Filter berdasarkan Rentang Tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Filter berdasarkan Pencarian (No Invoice atau Nama Pelanggan)
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function($subq) use ($search) {
                      $subq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                  });
            });
        }

        $transactions = $query->paginate(10)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    // 2. Menampilkan Rincian Detail Transaksi Tertentu
    public function show(Transaction $transaction)
    {
        $transaction->load(['details.product', 'user', 'customer', 'voucher']);
        return view('transactions.show', compact('transaction'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $query = Product::where('stock', '>', 0);

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(8)->withQueryString();
        
        return view('transactions.create', compact('products', 'categories')); 
    }

    public function checkVoucher(Request $request)
    {
        $voucher = Voucher::where('code', strtoupper($request->code))
                          ->where('is_active', true)
                          ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode Voucher tidak ditemukan atau tidak aktif.']);
        }

        if ($voucher->valid_until && $voucher->valid_until < date('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'Kode Voucher sudah kadaluarsa.']);
        }

        return response()->json([
            'success' => true,
            'data'    => $voucher,
            'message' => 'Voucher berhasil diterapkan!'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart_data'      => 'required|json',
            'paid_amount'    => 'required|numeric|min:0',
            'customer_phone' => 'nullable|string',
            'customer_name'  => 'nullable|string',
            'payment_method' => 'required|string',
            'voucher_code'   => 'nullable|string'
        ]);

        $cart = json_decode($request->cart_data, true);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang masih kosong!');
        }

        DB::beginTransaction();
        try {
            // 1. Hitung Subtotal Asli
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }

            $discount = 0;
            $customerId = null;
            $pesanHadiah = '';

            // 2. Cek Loyalitas Pelanggan (Transaksi ke-10)
            if (!empty($request->customer_phone)) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name ?? 'Pelanggan ' . $request->customer_phone]
                );
                $customerId = $customer->id;

                $totalTransaksi = Transaction::where('customer_id', $customerId)->count();
                $transaksiSekarang = $totalTransaksi + 1;

                if ($transaksiSekarang % 10 == 0) {
                    $setting = Setting::first();
                    $discount = $setting ? $setting->discount_amount : 10000;
                    $pesanHadiah = '<br><br>🎉 SELAMAT! Diskon Loyalitas Rp '.number_format($discount, 0, ',', '.').' & Sembako berhasil diterapkan! (Transaksi ke-'.$transaksiSekarang.')';
                }
            }

            // 3. Cek Voucher Umum (Jika tidak ada diskon loyalty)
            if (!empty($request->voucher_code) && $discount == 0) {
                $voucher = Voucher::where('code', strtoupper($request->voucher_code))
                                  ->where('is_active', true)
                                  ->where(function($q) {
                                      $q->whereNull('valid_until')->orWhere('valid_until', '>=', date('Y-m-d'));
                                  })->first();
                
                if ($voucher) {
                    if ($voucher->type == 'percent') {
                        $discount = $subtotal * ($voucher->value / 100);
                    } else {
                        $discount = $voucher->value;
                    }
                }
            }

            // 4. Kalkulasi Grand Total
            $grandTotal = $subtotal - $discount;
            if ($grandTotal < 0) { $grandTotal = 0; } 

            $paidAmount = $request->paid_amount;
            $changeAmount = $paidAmount - $grandTotal;

            if ($paidAmount < $grandTotal) {
                return redirect()->back()->with('error', 'Uang pembayaran kurang!');
            }

            // ==========================================
            // 5. CARI ID VOUCHER UNTUK DISIMPAN KE DATABASE
            // ==========================================
            $voucherId = null;
            if (!empty($request->voucher_code)) {
                $foundVoucher = Voucher::where('code', strtoupper($request->voucher_code))->first();
                if ($foundVoucher) {
                    $voucherId = $foundVoucher->id;
                }
            }

            $invoiceNumber = 'INV-' . date('YmdHis') . '-' . Auth::id();

            // 6. SIMPAN TRANSAKSI KE DATABASE (Termasuk voucher_id)
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id'        => Auth::id(),
                'customer_id'    => $customerId,
                'voucher_id'     => $voucherId, // <-- Relasi ID Voucher tersimpan di sini
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'grand_total'    => $grandTotal,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_method' => $request->payment_method,
            ]);

            // 7. Simpan Detail Transaksi & Potong Stok
            foreach ($cart as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $item['id'],
                    'quantity'       => $item['qty'],
                    'price'          => $item['price'],
                    'subtotal'       => $item['price'] * $item['qty'],
                ]);
                Product::where('id', $item['id'])->decrement('stock', $item['qty']);
            }

            DB::commit();
            
            return redirect()->route('transactions.create')
                ->with('success_transaction', 'Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.'))
                ->with('reward', $pesanHadiah)
                ->with('transaction_id', $transaction->id);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    public function receipt($id)
    {
        $transaction = Transaction::with(['details.product', 'user', 'customer', 'voucher'])->findOrFail($id);
        $setting = Setting::first();
        return view('transactions.receipt', compact('transaction', 'setting'));
    }
}