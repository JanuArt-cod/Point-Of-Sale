@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 fade-in-up gap-3">
        <div>
            <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Sistem Auth</span>
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-person-badge text-primary me-2"></i>MANAJEMEN PENGGUNA</h4>
        </div>
        
        <div class="d-flex gap-2">
            <form action="{{ route('users.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm border-0 shadow-sm rounded-start-pill px-3" placeholder="Cari nama / email..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm rounded-end-pill px-3"><i class="bi bi-search"></i></button>
            </form>
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-dark shadow-sm fw-bold rounded-pill px-3 d-flex align-items-center">
                <i class="bi bi-plus-circle me-1"></i> Tambah Akun
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm p-4 fade-in-up mb-5" style="background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px); border-radius: 20px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="background: transparent;">
                    <thead>
                        <tr style="background: rgba(0, 0, 0, 0.03); font-size: 0.8rem; letter-spacing: 0.5px;" class="text-uppercase text-muted">
                            <th class="py-3 text-center" style="width: 5%; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">No</th>
                            <th class="py-3" style="width: 30%;">Nama Lengkap</th>
                            <th class="py-3" style="width: 30%;">Email</th>
                            <th class="py-3 text-center" style="width: 15%;">Hak Akses</th>
                            <th class="py-3 text-center" style="width: 20%; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.05);">
                                <td class="text-center fw-bold text-muted">{{ $users->firstItem() + $index }}</td>
                                <td class="fw-bold text-dark">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-center">
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold">ADMIN</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-bold">KASIR</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm text-white shadow-sm" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm shadow-sm btn-delete" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                    Data pengguna tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- SCRIPT KONFIRMASI HAPUS DENGAN SWEETALERT2 -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: 'Hapus Akun?',
                        text: "Akun ini tidak akan bisa login lagi!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    })
                });
            });
        });
    </script>
@endsection