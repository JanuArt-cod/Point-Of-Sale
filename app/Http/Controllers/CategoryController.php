<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 2. Ambil semua data kategori, urutkan dari yang terbaru, paginasi 10 data per halaman
        $categories = Category::latest()->paginate(10);
        
        // 3. Kirim data ke tampilan views/categories/index.blade.php
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Menampilkan halaman form tambah
        return view('categories.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input agar tidak boleh kosong dan tidak boleh duplikat
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
            'name.unique' => 'Nama kategori ini sudah ada, silakan gunakan yang lain.'
        ]);

        // 2. Simpan ke database
        Category::create([
            'name' => $request->name
        ]);

        // 3. Kembalikan ke halaman daftar dengan pesan sukses
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // 1. Cari data kategori berdasarkan ID
        $category = Category::findOrFail($id);
        
        // 2. Tampilkan halaman edit dan kirim datanya
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        // 1. Validasi input (Pengecualian ID sendiri pada aturan unique agar tidak error saat disimpan ulang tanpa mengubah nama)
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
            'name.unique' => 'Nama kategori ini sudah ada, silakan gunakan yang lain.'
        ]);

        // 2. Perbarui data ke database
        $category->update([
            'name' => $request->name
        ]);

        // 3. Kembalikan ke halaman daftar dengan pesan sukses
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        // 1. Cari data lalu hapus
        $category = Category::findOrFail($id);
        $category->delete();

        // 2. Kembalikan ke halaman daftar
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
