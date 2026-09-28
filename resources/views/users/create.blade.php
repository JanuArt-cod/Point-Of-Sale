@extends('layouts.app')

@section('content')
<div class="row justify-content-center fade-in-up">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Pengguna</h4>
            <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-3">Kembali</a>
        </div>

        <div class="card border-0 shadow-sm p-4" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-radius: 20px;">
            <div class="card-body p-0">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email (Username Login)</label>
                            <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="contoh@kasir.com" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hak Akses</label>
                            <select name="role" class="form-select rounded-3 @error('role') is-invalid @enderror" required>
                                <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>KASIR (Hanya Melayani Transaksi)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ADMIN (Akses Penuh Master Data)</option>
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm">Simpan Akun</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection