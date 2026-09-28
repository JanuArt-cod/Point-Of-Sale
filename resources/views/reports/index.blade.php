@extends('layouts.app')

@section('content')
<style>
    /* Kunci body agar tidak scroll luar */
    html, body {
        height: 100vh;
        overflow: hidden;
        background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
    }

    .glass-card { 
        background: rgba(255, 255, 255, 0.75) !important; 
        backdrop-filter: blur(16px) !important; 
        border: 1px solid rgba(255, 255, 255, 0.5) !important; 
        border-radius: 20px; 
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important; 
    }

    /* Scroll khusus tabel */
    .table-scroll-container {
        max-height: calc(100vh - 430px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 5px;
    }
    .table-scroll-container::-webkit-scrollbar { width: 6px; }
    .table-scroll-container::-webkit-scrollbar-track { background: transparent; }
    .table-scroll-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
</style>

<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <!-- Header & Filter Tanggal -->
            <div class="glass-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Laporan Penjualan</h5>
                    <button onclick="window.print()" class="btn btn-dark btn-sm fw-bold rounded-pill px-3 shadow-sm">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan
                    </button>
                </div>

                <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.75rem;">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm bg-white bg-opacity-75" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.75rem;">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm bg-white bg-opacity-75" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm"><i class="bi bi-filter"></i> Filter Laporan</button>
                        <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm border w-50" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>

            <!-- Kartu Statistik Ringkasan -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="glass-card p-3 text-center">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Total Pendapatan</small>
                        <h5 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card p-3 text-center">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Total Transaksi</small>
                        <h5 class="fw-bold text-primary mb-0 mt-1">{{ $totalTransactions }} Transaksi</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card p-3 text-center">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Total Diskon Diberikan</small>
                        <h5 class="fw-bold text-danger mb-0 mt-1">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card p-3 text-center">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Rata-rata / Transaksi</small>
                        <h5 class="fw-bold text-dark mb-0 mt-1">Rp {{ number_format($averageSales, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Laporan -->
            <div class="glass-card p-4">
                <div class="table-scroll-container">
                    <table class="table align-middle table-borderless mb-0">
                        <thead class="sticky-top bg-white bg-opacity-75" style="backdrop-filter: blur(10px); z-index: 5;">
                            <tr class="text-uppercase text-muted border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <th class="py-2">No. Invoice</th>
                                <th class="py-2">Waktu</th>
                                <th class="py-2">Pelanggan</th>
                                <th class="py-2">Kasir</th>
                                <th class="py-2 text-center">Metode</th>
                                <th class="py-2 text-end">Diskon</th>
                                <th class="py-2 text-end">Total Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr class="border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
                                    <td class="fw-bold text-primary py-2">{{ $trx->invoice_number }}</td>
                                    <td class="text-secondary py-2" style="font-size: 0.85rem;">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">{{ $trx->customer->name ?? 'Umum' }}</td>
                                    <td class="py-2 text-secondary">{{ $trx->user->name ?? '-' }}</td>
                                    <td class="text-center py-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark text-uppercase px-2 py-1" style="font-size: 0.70rem;">
                                            {{ $trx->payment_method }}
                                        </span>
                                    </td>
                                    <td class="text-end text-danger py-2">- Rp {{ number_format($trx->discount, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-success py-2">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-file-earmark-x fs-1 opacity-25 d-block mb-2"></i>
                                        <h5>Tidak ada data laporan pada rentang tanggal tersebut.</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection