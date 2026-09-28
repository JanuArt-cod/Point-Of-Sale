@extends('layouts.app')

@section('content')
<style>
    /* 1. Kunci body agar tidak bisa scroll ke bawah */
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

    /* 2. Batasi area kontainer tabel dan berikan scroll khusus di dalam tabel saja */
    .table-scroll-container {
        max-height: calc(100vh - 360px); /* Menyesuaikan tinggi layar agar tabel saja yang scroll */
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 5px;
    }

    /* Kustomisasi scrollbar agar rapi ala iOS */
    .table-scroll-container::-webkit-scrollbar { width: 6px; }
    .table-scroll-container::-webkit-scrollbar-track { background: transparent; }
    .table-scroll-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
</style>

<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <!-- Header & Filter (Diam / Tidak Ikut Scroll) -->
            <div class="glass-card p-3 mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Transaksi</h5>
                    </div>
                    <div>
                        <!-- Tombol Pintasan ke Kasir POS -->
                        <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm fw-bold rounded-pill px-3 shadow-sm">
                            <i class="bi bi-cash-register me-1"></i> Buka Kasir (POS)
                        </a>
                    </div>
                </div>

                <form action="{{ route('transactions.index') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.75rem;">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm bg-white bg-opacity-75" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.75rem;">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm bg-white bg-opacity-75" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.75rem;">Cari Invoice / Pelanggan</label>
                        <input type="text" name="search" class="form-control form-control-sm bg-white bg-opacity-75" placeholder="No. Invoice atau No. HP..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm"><i class="bi bi-filter"></i> Filter</button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-light btn-sm border w-50" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>

            <!-- Tabel Riwayat dengan Scroll Khusus -->
            <div class="glass-card p-4">
                <div class="table-scroll-container">
                    <table class="table align-middle table-borderless mb-0">
                        <thead class="sticky-top bg-white bg-opacity-75" style="backdrop-filter: blur(10px); z-index: 5;">
                            <tr class="text-uppercase text-muted border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <th class="py-2">No. Invoice</th>
                                <th class="py-2">Waktu</th>
                                <th class="py-2">Pelanggan</th>
                                <th class="py-2">Kasir</th>
                                <th class="py-2">Total Belanja</th>
                                <th class="py-2 text-center">Metode</th>
                                <th class="py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr class="border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
                                    <td class="fw-bold text-primary py-2">{{ $trx->invoice_number }}</td>
                                    <td class="text-secondary py-2" style="font-size: 0.85rem;">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">
                                        {{ $trx->customer->name ?? 'Umum' }}
                                        @if($trx->customer)
                                            <br><small class="text-muted">{{ $trx->customer->phone }}</small>
                                        @endif
                                    </td>
                                    <td class="py-2 text-secondary">{{ $trx->user->name ?? '-' }}</td>
                                    <td class="fw-bold text-success py-2">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                    <td class="text-center py-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark text-uppercase px-2 py-1" style="font-size: 0.70rem;">
                                            {{ $trx->payment_method }}
                                        </span>
                                    </td>
                                    <td class="text-center py-2">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('transactions.show', $trx->id) }}" class="btn btn-info btn-sm text-white rounded-pill px-3 shadow-sm" style="font-size: 0.75rem;" title="Lihat Rincian">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <a href="{{ route('transactions.receipt', $trx->id) }}" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" style="font-size: 0.75rem;" title="Cetak Struk">
                                                <i class="bi bi-printer"></i> Struk
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-receipt fs-1 opacity-25 d-block mb-2"></i>
                                        <h5>Tidak ada riwayat transaksi ditemukan.</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3 d-flex justify-content-center">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection