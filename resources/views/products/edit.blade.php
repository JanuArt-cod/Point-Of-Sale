@extends('layouts.app')

@section('content')
<div class="row justify-content-center fade-in-up">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Produk</h4>
            <a href="{{ route('products.index') }}" class="btn btn-secondary shadow-sm">Kembali</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Harga Jual</label>
                            <input type="number" name="selling_price" class="form-control" value="{{ $product->selling_price }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Harga Beli</label>
                            <input type="number" name="purchase_price" class="form-control" value="{{ $product->purchase_price }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Stok</label>
                        <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Ganti Foto Produk (Kosongkan jika tidak diganti)</label>
                        @if(!empty($product->image))
                            <div class="mb-2">
                                <img src="{{ asset('uploads/products/' . $product->image) }}" class="rounded border" width="100">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">Update Produk</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection