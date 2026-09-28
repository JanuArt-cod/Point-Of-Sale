@extends('layouts.app')

@section('content')
    <div class="fade-in-up mb-3 flex-shrink-0 d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Master Data</span>
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>EDIT KATEGORI</h4>
        </div>
        <!-- Tombol Kembali -->
        <a href="{{ route('categories.index') }}" class="btn btn-secondary rounded-pill shadow-sm px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row flex-grow-1 justify-content-center mt-4">
        <div class="col-md-6 col-lg-5">
            <div class="card glass-animated-card p-4 fade-in-up delay-1 border-0">
                <div class="card-body">
                    
                    <!-- Form Action mengarah ke fungsi update() dan membutuhkan method PUT -->
                    <form action="{{ route('categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark ms-1">Nama Kategori</label>
                            <!-- Menampilkan data lama sebagai default value -->
                            <input type="text" class="form-control ios-input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" autofocus autocomplete="off">
                            
                            @error('name')
                                <div class="invalid-feedback ms-2 fw-bold">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn ios-btn w-100 shadow-sm mt-2">
                            <i class="bi bi-check2-circle me-2"></i> Perbarui Kategori
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection