@extends('layouts.app')

@section('content')
<div class="row justify-content-center fade-in-up">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-person-gear text-warning me-2"></i>Edit Pengguna</h4>
            <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-3">Kembali</a>
        </div>

        <div class="card border-0 shadow-sm p-4" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-radius: 20px;">
            <div class="card-body p-0">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email (Username Login)</label>
                            <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror" placeholder="Biarkan kosong jika tidak diubah">
                            <small class="text-muted" style="font-size: 0.75rem;">Isi hanya jika ingin mengganti password lama.</small>
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hak Akses</label>
                            <select name="role" class="form-select rounded-3 @error('role') is-invalid @enderror" required>
                                <option value="kasir" {{ (old('role', $user->role) == 'kasir') ? 'selected' : '' }}>KASIR (Hanya Melayani Transaksi)</option>
                                <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>ADMIN (Akses Penuh Master Data)</option>
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm">Update Akun</button>
                </form>
                <div class="mb-3">
    <label class="form-label fw-bold">Hak Akses Menu (Hak Akses & Keamanan)</label>
    <div class="row g-2 bg-white bg-opacity-50 p-3 rounded-3 border">
        @php
            $menus = [
                'categories' => 'Kelola Kategori',
                'products' => 'Kelola Produk',
                'customers' => 'Data Pelanggan',
                'transactions' => 'Riwayat Transaksi',
                'reports' => 'Laporan Penjualan',
                'users' => 'Manajemen Pengguna',
                'settings' => 'Pengaturan'
            ];
            $userPermissions = $user->permissions ?? [];
        @endphp

        @foreach($menus as $key => $label)
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}" id="menu_{{ $key }}"
                    {{ in_array($key, $userPermissions) ? 'checked' : '' }}>
                <label class="form-check-label text-dark" for="menu_{{ $key }}">
                    {{ $label }}
                </label>
            </div>
        </div>
        @endforeach
    </div>
    <small class="text-muted">Admin Utama otomatis memiliki akses ke seluruh menu tanpa batasan.</small>
</div>
            </div>
        </div>
    </div>
</div>
@endsection