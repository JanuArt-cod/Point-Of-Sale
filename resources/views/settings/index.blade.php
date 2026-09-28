@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <h4 class="fw-bold mb-4"><i class="bi bi-gear text-primary me-2"></i>PENGATURAN APLIKASI & VOUCHER</h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 1. FORM PENGATURAN TOKO -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="fw-bold text-muted mb-3 border-bottom pb-2">Informasi Toko & Struk</h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Toko</label>
                    <input type="text" name="store_name" class="form-control" value="{{ $setting->store_name ?? 'POS APP' }}" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. Telepon</label>
                        <input type="text" name="store_phone" class="form-control" value="{{ $setting->store_phone ?? '0812...' }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Logo Toko (Opsional)</label>
                        <input type="file" name="store_logo" class="form-control" accept="image/*">
                        @if(!empty($setting->store_logo))
                            <small class="text-success d-block mt-1"><i class="bi bi-check-circle"></i> Logo sudah terpasang</small>
                        @endif
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Alamat Toko</label>
                    <textarea name="store_address" class="form-control" rows="2" required>{{ $setting->store_address ?? 'Jl. Raya...' }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm">
                    Simpan Perubahan Toko
                </button>
            </form>
        </div>

        <!-- 2. MANAJEMEN VOUCHER & KODE PROMO -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold text-muted mb-3 border-bottom pb-2">Manajemen Kode Voucher & Promo</h5>
            
            <!-- Form Tambah Voucher -->
            <form action="{{ route('settings.voucher.store') }}" method="POST" class="row g-3 mb-4 bg-light p-3 rounded-4">
                @csrf
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Kode Voucher</label>
                    <input type="text" name="code" class="form-control text-uppercase" placeholder="Cth: LEBARAN" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Tipe Potongan</label>
                    <select name="type" class="form-select" required>
                        <option value="nominal">Nominal (Rp)</option>
                        <option value="percent">Persentase (%)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Nilai Potongan</label>
                    <input type="number" name="value" class="form-control" placeholder="Cth: 10000 atau 10" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Masa Berlaku</label>
                    <input type="date" name="valid_until" class="form-control">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-dark fw-bold px-4 rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Voucher
                    </button>
                </div>
            </form>

            <!-- Tabel Daftar Voucher -->
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-uppercase text-muted" style="font-size: 0.8rem;">
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Berlaku Sampai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                            <tr>
                                <td class="fw-bold text-primary">{{ $voucher->code }}</td>
                                <td>{{ ucfirst($voucher->type) }}</td>
                                <td class="fw-bold">
                                    @if($voucher->type == 'percent')
                                        {{ $voucher->value }}%
                                    @else
                                        Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>{{ $voucher->valid_until ? date('d M Y', strtotime($voucher->valid_until)) : 'Selamanya' }}</td>
                                <td class="text-center">
                                    <form action="{{ route('settings.voucher.destroy', $voucher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" onclick="return confirm('Hapus voucher ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada voucher aktif yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection