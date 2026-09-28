<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rute otomatis untuk Login, Register, Logout dari Laravel UI
Auth::routes();

Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Kategori
    Route::resource('categories', CategoryController::class);
    
    // Produk - Rute Kustom (Wajib diletakkan di atas rute resource)
    Route::get('products/template-csv', [ProductController::class, 'downloadCsvTemplate'])->name('products.template-csv');
    Route::post('products/import', [ProductController::class, 'importCsv'])->name('products.import');
    Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulkDelete');
    // Produk - Rute Resource (CRUD)
    Route::resource('products', ProductController::class);

    // Rute Rekap Pelanggan (Hanya Index)
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('auth');

    // Rute Cetak Struk (Letakkan di ATAS route resource)
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt')->middleware('auth');
    // Rute untuk mengecek validitas Voucher via AJAX
    Route::post('/transactions/check-voucher', [TransactionController::class, 'checkVoucher'])->name('transactions.check_voucher')->middleware('auth');
    // Rute Transaksi & Kasir
    Route::resource('transactions', TransactionController::class)->middleware('auth');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('auth');
    

    // Route Management Users
    Route::resource('users', UserController::class)->middleware('auth');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index')->middleware('auth');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('auth');
    Route::post('/settings/voucher', [SettingController::class, 'storeVoucher'])->name('settings.voucher.store')->middleware('auth');
    Route::delete('/settings/voucher/{voucher}', [SettingController::class, 'destroyVoucher'])->name('settings.voucher.destroy')->middleware('auth');

    
    
    
});