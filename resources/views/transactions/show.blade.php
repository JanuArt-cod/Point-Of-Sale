@extends('layouts.app')

@section('content')
<style>
    body { background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%); background-attachment: fixed; }
    .glass-card { background: rgba(255, 255, 255, 0.75) !important; backdrop-filter: blur(16px) !important; border: 1px solid rgba(255, 255, 255, 0.5) !important; border-radius: 20px; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important; }
</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-file-text text-primary me-2"></i>Rincian Transaksi</h4>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary rounded-pill px-4 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="glass-card p-4 mb-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">No. Invoice</small>
                    <h5 class="fw-bold text-primary">{{ $transaction->invoice_number }}</h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted d-block">Waktu Transaksi</small>
                    <span class="fw-bold">{{ $transaction->created_at->format('d F Y, H:i') }}</span>
                </div>
            </div>

            <hr class="text-muted">

            <div class="row mb-4">
                <div class="col-md-4">
                    <small class="text-muted d-block">Kasir / User</small>
                    <span class="fw-bold text-dark">{{ $transaction->user->name ?? '-' }}</span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Pelanggan</small>
                    <span class="fw-bold text-dark">{{ $transaction->customer->name ?? 'Umum' }} ({{ $transaction->customer->phone ?? '-' }})</span>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-muted d-block">Metode Pembayaran</small>
                    <span class="badge bg-success bg-opacity-10 text-success text-uppercase px-3 py-1 fw-bold">
                        {{ $transaction->payment_method }}
                    </span>
                </div>
            </div>

            <!-- Tabel Produk yang Dibeli -->
            <h6 class="fw-bold text-dark mb-3">Daftar Barang</h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle table-bordered bg-white bg-opacity-50 rounded-3 overflow-hidden">
                    <thead class="table-light">
                        <tr style="font-size: 0.85rem;">
                            <th>Nama Produk</th>
                            <th class="text-center">Harga Satuan</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->details as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                            <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Rincian Nominal -->
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-bold">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($transaction->discount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Diskon:</span>
                        <span class="fw-bold text-danger">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2 border-top pt-2">
                        <span class="fw-bold text-dark">Total Akhir:</span>
                        <span class="fw-bold text-primary fs-5">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tunai Dibayar:</span>
                        <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Kembalian:</span>
                        <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 pt-3 border-top">
                <a href="{{ route('transactions.receipt', $transaction->id) }}" target="_blank" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-printer me-1"></i> Cetak Ulang Struk
                </a>
            </div>
        </div>

    </div>
</div>
@endsection