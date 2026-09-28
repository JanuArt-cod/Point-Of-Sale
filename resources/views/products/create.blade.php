@extends('layouts.app')

@section('content')
    <div class="fade-in-up mb-4 d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Master Data</span>
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-plus-circle text-primary me-2"></i>TAMBAH PRODUK</h4>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-pill shadow-sm px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Dua Card Berdampingan dengan tinggi normal (bisa di-scroll) -->
    <div class="row g-4 pb-4">
        
        <!-- CARD 1: INPUT MANUAL -->
        <div class="col-md-6">
            <div class="card glass-animated-card p-4 fade-in-up delay-1 border-0 h-100">
                <div class="card-header bg-transparent border-0 pb-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-keyboard text-primary me-2"></i>Form Input Manual</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark ms-1">Nama Produk</label>
                            <input type="text" class="form-control ios-input @error('name') is-invalid @enderror" name="name" placeholder="Contoh: Aqua Galon 19L" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback ms-2">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark ms-1">Kategori</label>
                            <select name="category_id" class="form-select ios-input @error('category_id') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback ms-2">{{ $message }}</div>@enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark ms-1">Harga Jual (Rp)</label>
                                <input type="number" class="form-control ios-input @error('selling_price') is-invalid @enderror" name="selling_price" placeholder="20000" value="{{ old('selling_price') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark ms-1">Harga Beli / Modal</label>
                                <input type="number" class="form-control ios-input" name="purchase_price" placeholder="Opsional" value="{{ old('purchase_price') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark ms-1">Stok Awal</label>
                            <input type="number" class="form-control ios-input @error('stock') is-invalid @enderror" name="stock" placeholder="15" value="{{ old('stock') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark ms-1">Foto Produk (Opsional)</label>
                            <input type="file" class="form-control ios-input" name="image" accept="image/*">
                        </div>

                        <button type="submit" class="btn ios-btn w-100 shadow-sm fw-bold">
                            <i class="bi bi-save me-2"></i> Simpan Produk
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CARD 2: UPLOAD CSV -->
        <div class="col-md-6">
            <div class="card glass-animated-card p-4 fade-in-up delay-2 border-0 h-100">
                <div class="card-header bg-transparent border-0 pb-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Import Data via CSV</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    
                    <div class="alert alert-info bg-white bg-opacity-50 border-0 p-4 rounded-4 mb-4 shadow-sm">
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle me-1"></i> Petunjuk Format CSV:</h6>
                        <p class="text-muted mb-2" style="font-size: 0.9rem;">Pastikan baris pertama adalah header, diikuti urutan kolom: <br><b>ID Kategori, Nama Produk, Harga Jual, Harga Beli, Stok</b></p>
                        <a href="{{ route('products.template-csv') }}" class="text-decoration-none fw-bold"><i class="bi bi-download me-1"></i> Unduh Contoh Format CSV</a>
                    </div>

                    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark ms-1">Pilih File CSV/TXT</label>
                            <input type="file" class="form-control ios-input @error('csv_file') is-invalid @enderror" name="csv_file" accept=".csv, .txt" required>
                            @error('csv_file')<div class="invalid-feedback ms-2">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100 shadow-sm fw-bold rounded-pill py-3">
                            <i class="bi bi-upload me-2"></i> Upload & Import Data CSV
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection