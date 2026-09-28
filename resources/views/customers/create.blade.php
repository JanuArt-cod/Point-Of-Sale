@extends('layouts.app')

@section('content')
<div class="row justify-content-center fade-in-up">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Pelanggan</h4>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-3">Kembali</a>
        </div>

        <div class="card border-0 shadow-sm p-4" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-radius: 20px;">
            <div class="card-body p-0">
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ old('name') }}" placeholder="Masukkan nama pelanggan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No. Telepon/WhatsApp</label>
                        <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone') }}" placeholder="Contoh: 08123456789">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea name="address" class="form-control rounded-3" rows="3" placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm">Simpan Pelanggan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection