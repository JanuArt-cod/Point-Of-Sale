@extends('layouts.app')

@section('content')
    <!-- HEADER HALAMAN & PENCARIAN -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 fade-in-up gap-3">
        <div>
            <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Master Data</span>
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam text-primary me-2"></i>DATA PRODUK</h4>
        </div>
        
        <!-- Kolom Pencarian & Tombol Tambah -->
        <div class="d-flex gap-2">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm border-0 shadow-sm rounded-start-pill px-3" placeholder="Cari nama produk..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm rounded-end-pill px-3"><i class="bi bi-search"></i></button>
            </form>
            <a href="{{ route('products.create') }}" class="btn btn-sm btn-dark shadow-sm fw-bold rounded-pill px-3 d-flex align-items-center">
                <i class="bi bi-plus-circle me-1"></i> Tambah
            </a>
        </div>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert" style="background: rgba(25, 135, 84, 0.1); color: #155724;">
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

    <!-- FORM UNTUK BULK DELETE -->
    <form action="{{ route('products.bulkDelete') }}" method="POST" id="bulkDeleteForm">
        @csrf
        
        <!-- Tombol Hapus Terpilih (Awalnya Disembunyikan) -->
        <div class="mb-3 fade-in-up" id="bulkDeleteContainer" style="display: none;">
            <button type="button" class="btn btn-danger btn-sm shadow-sm rounded-pill px-3 fw-bold" id="btnConfirmBulk">
                <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
        </div>

        <div class="card border-0 shadow-sm p-4 fade-in-up mb-5" style="background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px); border-radius: 20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr style="background: rgba(0, 0, 0, 0.03); font-size: 0.8rem; letter-spacing: 0.5px;" class="text-uppercase text-muted">
                                <!-- Checkbox Select All -->
                                <th class="py-3 text-center" style="width: 4%; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </th>
                                <th class="py-3 text-center" style="width: 5%;">No</th>
                                <th class="py-3 text-center" style="width: 10%;">Foto</th>
                                <th class="py-3" style="width: 20%;">Kategori</th>
                                <th class="py-3" style="width: 26%;">Nama Produk</th>
                                <th class="py-3" style="width: 15%;">Harga</th>
                                <th class="py-3 text-center" style="width: 10%;">Stok</th>
                                <th class="py-3 text-center" style="width: 10%; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $product)
                                <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.05);">
                                    <!-- Checkbox Satuan -->
                                    <td class="text-center">
                                        <input class="form-check-input chk-item" type="checkbox" name="ids[]" value="{{ $product->id }}">
                                    </td>
                                    <td class="text-center fw-bold text-muted">{{ $products->firstItem() + $index }}</td>
                                    <td class="text-center">
                                        @if(!empty($product->image))
                                            <img src="{{ asset('uploads/products/' . $product->image) }}" class="rounded shadow-sm" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <span class="badge bg-secondary opacity-50 px-2 py-1" style="font-size: 0.65rem;">No Image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-dark bg-opacity-75 rounded-pill px-3 py-2 fw-normal" style="font-size: 0.75rem;">
                                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">{{ $product->name }}</td>
                                    <td class="text-primary fw-bold">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="{{ $product->stock <= 5 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">{{ $product->stock }}</span>
                                    </td>
                                    <td class="text-center">
                                        <!-- Tombol Edit saja, tombol Hapus dipisah dari form bulk agar tidak bentrok -->
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm text-white shadow-sm" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                        @if(request('search'))
                                            Produk "<b>{{ request('search') }}</b>" tidak ditemukan.
                                        @else
                                            Belum ada data produk yang tersedia.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top gap-3" style="border-color: rgba(0, 0, 0, 0.08) !important;">
                        <div class="text-muted" style="font-size: 0.85rem;">
                            Menampilkan <b>{{ $products->firstItem() }}</b> sampai <b>{{ $products->lastItem() }}</b> dari <b>{{ $products->total() }}</b> total data
                        </div>
                        <div>
                            {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
    <!-- SCRIPT LOGIKA CHECKBOX DAN SWEETALERT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const chkItems = document.querySelectorAll('.chk-item');
            const bulkDeleteContainer = document.getElementById('bulkDeleteContainer');
            const selectedCount = document.getElementById('selectedCount');
            const btnConfirmBulk = document.getElementById('btnConfirmBulk');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');

            // Fungsi untuk update tampilan tombol hapus massal
            function toggleBulkDeleteButton() {
                const checkedCount = document.querySelectorAll('.chk-item:checked').length;
                selectedCount.textContent = checkedCount;
                if (checkedCount > 0) {
                    bulkDeleteContainer.style.display = 'block';
                } else {
                    bulkDeleteContainer.style.display = 'none';
                    selectAll.checked = false;
                }
            }

            // Centang semua
            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    chkItems.forEach(item => {
                        item.checked = this.checked;
                    });
                    toggleBulkDeleteButton();
                });
            }

            // Centang satuan
            chkItems.forEach(item => {
                item.addEventListener('change', function() {
                    toggleBulkDeleteButton();
                    // Cek apakah semua item terpilih
                    if(document.querySelectorAll('.chk-item:checked').length === chkItems.length) {
                        selectAll.checked = true;
                    } else {
                        selectAll.checked = false;
                    }
                });
            });

            // SweetAlert untuk Hapus Massal
            if(btnConfirmBulk) {
                btnConfirmBulk.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus Data Terpilih?',
                        text: "Data dan foto akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            bulkDeleteForm.submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection