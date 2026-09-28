@extends('layouts.app')

@section('content')
<style>
    body { background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%); background-attachment: fixed; }
    .glass-card { background: rgba(255, 255, 255, 0.75) !important; backdrop-filter: blur(16px) !important; border: 1px solid rgba(255, 255, 255, 0.5) !important; border-radius: 20px; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important; }
</style>

<div class="row justify-content-center">
    <div class="col-md-11">
        
        <!-- Header Judul -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Pelanggan (CRM)</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola data pelanggan dan pantau status loyalitas secara otomatis.</p>
            </div>
        </div>

        <!-- Tabel Glassmorphism -->
        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table align-middle table-borderless mb-0">
                    <thead>
                        <tr class="text-uppercase text-muted border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th class="py-3" style="width: 8%;">Peringkat</th>
                            <th class="py-3" style="width: 25%;">Nama Pelanggan</th>
                            <th class="py-3" style="width: 25%;">No. Telepon</th>
                            <th class="py-3 text-center" style="width: 20%;">Total Transaksi</th>
                            <th class="py-3 text-center" style="width: 22%;">Status Member</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                            <tr class="border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
                                <!-- Peringkat -->
                                <td class="fw-bold text-muted py-3">
                                    #{{ $customers->firstItem() + $index }}
                                </td>

                                <!-- Nama Pelanggan -->
                                <td class="fw-bold text-dark py-3">
                                    {{ $customer->name }}
                                </td>

                                <!-- No Telepon -->
                                <td class="text-secondary py-3">
                                    <i class="bi bi-telephone-inbound text-muted me-1"></i> {{ $customer->phone }}
                                </td>

                                <!-- Total Transaksi (Badge Biru) -->
                                <td class="text-center py-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem;">
                                        {{ $customer->transactions_count }}x Transaksi
                                    </span>
                                </td>

                                <!-- Status Member (VIP jika >= 10, Reguler jika < 10) -->
                                <td class="text-center py-3">
                                    @if($customer->transactions_count >= 10)
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.8rem;">
                                            <i class="bi bi-star-fill text-danger me-1"></i> VIP / Bingkisan
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-25 text-dark px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                                            Reguler
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-person-x fs-1 opacity-25 d-block mb-2"></i>
                                    <h5>Belum ada data pelanggan yang tersimpan.</h5>
                                    <small>Pelanggan akan otomatis tercatat saat kasir memasukkan nomor HP pada menu transaksi.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection