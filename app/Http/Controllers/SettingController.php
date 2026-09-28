<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first() ?? new Setting();
        $vouchers = Voucher::latest()->get(); // Ambil daftar voucher
        return view('settings.index', compact('setting', 'vouchers'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name'         => 'required|string|max:255',
            'store_address'      => 'required|string',
            'store_phone'        => 'required|string',
            'discount_threshold' => 'required|numeric|min:0',
            'discount_amount'    => 'required|numeric|min:0',
            'store_logo'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $setting = Setting::first() ?? new Setting();
        $setting->fill($request->only([
            'store_name', 'store_address', 'store_phone', 
            'discount_threshold', 'discount_amount'
        ]));

        if ($request->hasFile('store_logo')) {
            $file = $request->file('store_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/logo'), $filename);
            $setting->store_logo = $filename;
        }

        $setting->save();

        return redirect()->route('settings.index')->with('success', 'Pengaturan toko berhasil diperbarui!');
    }

    // FUNGSI TAMBAH VOUCHER
    public function storeVoucher(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|unique:vouchers,code|max:50',
            'type'        => 'required|in:nominal,percent',
            'value'       => 'required|numeric|min:0',
            'valid_until' => 'nullable|date',
        ]);

        Voucher::create([
            'code'        => strtoupper($request->code),
            'type'        => $request->type,
            'value'       => $request->value,
            'valid_until' => $request->valid_until,
            'is_active'   => true,
        ]);

        return redirect()->route('settings.index')->with('success', 'Kode voucher baru berhasil ditambahkan!');
    }

    // FUNGSI HAPUS VOUCHER
    public function destroyVoucher(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('settings.index')->with('success', 'Voucher berhasil dihapus!');
    }
}