@extends('layouts.app')

@section('content')
    <div class="fade-in-up mb-3 flex-shrink-0 d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Master Data</span>
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-tags text-primary me-2"></i>KATEGORI PRODUK</h4>
        </div>
        <!-- Tombol Tambah Data -->
        <a href="{{ route('categories.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
            <i class="bi bi-plus-circle me-1"></i> Tambah Baru
        </a>
    </div>
    <!-- ... (Sisa kode header judul halaman di atasnya) ... -->
    
    <!-- Tampilkan Notifikasi Sukses Jika Ada -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-pill glass-animated-card border-0 mb-3" role="alert">
            <i class="bi bi-check-circle-fill text-success me-2"></i> <span class="text-dark fw-bold">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row flex-grow-1">
        <div class="col-md-12 h-100 pb-2">
            <div class="card glass-animated-card p-3 fade-in-up delay-1 h-100 border-0 d-flex flex-column">
                <div class="card-body overflow-auto flex-grow-1 p-0">
                    <!-- Hapus class table-hover dan table-borderless, tambahkan glass-table -->
                    <table class="table glass-table align-middle">
                        <!-- Hapus bg-light bg-opacity-50 agar benar-benar transparan -->
                        <thead>
                            <tr>
                                <th width="5%" class="text-center py-3">No</th>
                                <th class="py-3">Nama Kategori</th>
                                <th width="15%" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $index => $category)
                                <!-- Hapus border-bottom border-light karena sudah diurus oleh CSS glass-table -->
                                <tr>
                                    <td class="text-center text-muted">{{ $categories->firstItem() + $index }}</td>
                                    <td class="fw-bold text-dark">{{ $category->name }}</td>
                                    <td class="text-center">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning rounded-pill text-white shadow-sm px-3">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <!-- Tombol Hapus (Dibungkus Form) -->
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" id="delete-form-{{ $category->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <!-- Ubah type menjadi button dan tambahkan onclick -->
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill shadow-sm px-3" onclick="confirmDelete({{ $category->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted border-0">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-primary opacity-50"></i>
                                        Belum ada data kategori.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Area Paginasi -->
                <div class="card-footer bg-transparent border-0 pt-3">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
        
    </div>
    <!-- SCRIPT SWEETALERT2 -->
    <script>
        // 1. Pop-up Notifikasi Sukses (Untuk Tambah/Edit/Hapus)
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000,
                background: 'rgba(255, 255, 255, 0.9)',
                backdrop: 'rgba(0,0,0,0.1)',
                customClass: {
                    popup: 'glass-animated-card border-0'
                }
            });
        @endif

        // 2. Pop-up Konfirmasi Hapus
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: 'rgba(255, 255, 255, 0.9)',
                backdrop: 'rgba(0,0,0,0.3)',
                customClass: {
                    popup: 'glass-animated-card border-0' // Menyuntikkan efek kaca iOS kita
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik "Ya", jalankan form hapus secara otomatis
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection