<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default filter: Bulan ini jika tanggal kosong
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        // Query transaksi berdasarkan rentang tanggal
        $query = Transaction::with(['user', 'customer', 'details.product'])
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->latest();

        $transactions = $query->get();

        // Hitung Ringkasan (Statistik)
        $totalRevenue = $transactions->sum('grand_total');
        $totalTransactions = $transactions->count();
        $totalDiscount = $transactions->sum('discount');
        $averageSales = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return view('reports.index', compact(
            'transactions', 'startDate', 'endDate', 
            'totalRevenue', 'totalTransactions', 'totalDiscount', 'averageSales'
        ));
    }
}