<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung total data dari database yang sebenarnya
        $total_products = Product::count();
        $total_customers = Customer::count();
        
        // Logika penjualan
        $sales_today = Transaction::whereDate('created_at', today())->count();
        $revenue_today = Transaction::whereDate('created_at', today())->sum('grand_total');

        // Total Profit (Akumulasi grand_total seluruh transaksi)
        $total_profit = Transaction::sum('grand_total');

        // Mengambil produk yang stoknya di bawah 10 (Batasi 3 data)
        $limit_stocks = Product::where('stock', '<', 10)->take(3)->get();

        // Data untuk Chart: Produk Terlaris (Best Selling)
        $bestSelling = TransactionDetail::select('product_id', DB::raw('SUM(quantity) as total_qty'))
                        ->groupBy('product_id')
                        ->orderByDesc('total_qty')
                        ->take(5)
                        ->with('product')
                        ->get();

        $chartLabels = $bestSelling->map(fn($item) => $item->product->name ?? 'Produk Dihapus');
        $chartData = $bestSelling->pluck('total_qty');

        // Mengirim data ke file view dashboard
        return view('dashboard', compact(
            'total_products', 
            'total_customers', 
            'sales_today', 
            'revenue_today',
            'total_profit',
            'limit_stocks',
            'chartLabels',
            'chartData'
        ));
    }
}