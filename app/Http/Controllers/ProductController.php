<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        // 1. FITUR PENCARIAN (Search)
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }

        // withQueryString() digunakan agar saat pindah halaman (pagination), kata kunci pencarian tidak hilang
        $products = $query->paginate(10)->withQueryString();

        return view('products.index', compact('products'));
    }
    // Debugging kilat: Cek apakah file masuk
    // dd($request->all(), $request->file('image'));
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua kategori untuk pilihan dropdown di form
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:255',
            'selling_price'  => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imageName = null;

        // Menggunakan metode move() manual yang aman dari error disk
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
            
            // Buat folder public/uploads/products secara otomatis jika belum ada
            $destinationPath = public_path('uploads/products');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Pindahkan file fisik ke folder tujuan
            $image->move($destinationPath, $imageName);
        }

        $generatedBarcode = 'BRC' . date('ymdHis') . rand(10, 99);
        $purchasePrice = $request->purchase_price ?? $request->selling_price;

        // Simpan ke database
        Product::create([
            'category_id'    => $request->category_id,
            'name'           => $request->name,
            'barcode'        => $generatedBarcode,
            'selling_price'  => $request->selling_price,
            'purchase_price' => $purchasePrice,
            'stock'          => $request->stock,
            'image'          => $imageName, // Nama file dipastikan masuk ke database!
        ]);

        return redirect()->route('products.index')->with('success', 'Produk baru berhasil ditambahkan!');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');
        $fileData = fopen($file->getRealPath(), 'r');
        
        fgetcsv($fileData); // Lewati header

        while (($row = fgetcsv($fileData, 1000, ',')) !== false) {
            // Asumsi CSV: category_id, name, selling_price, purchase_price, stock
            Product::create([
                'category_id'    => $row[0],
                'name'           => $row[1],
                'barcode'        => 'BRC' . date('ymdHis') . rand(10,99),
                'selling_price'  => $row[2], // Perbaikan di sini
                'purchase_price' => $row[3] ?? $row[2],
                'stock'          => $row[4] ?? 0,
            ]);
        }

        fclose($fileData);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil di-import dari CSV!');
    }
   public function downloadCsvTemplate()
    {
        $fileName = 'format_import_produk.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['category_id', 'name', 'selling_price', 'purchase_price', 'stock'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Contoh data
            fputcsv($file, ['1', 'Kopi Susu Gula Aren', '18000', '12000', '50']);
            fputcsv($file, ['1', 'Roti Bakar Coklat', '15000', '10000', '30']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
    // Tampilkan Halaman Form Edit
    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // Proses Simpan Perubahan (Update)
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:255',
            'selling_price'  => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imageName = $product->image; // Gunakan nama gambar lama sebagai default

        // Jika user mengunggah gambar baru
        if ($request->hasFile('image')) {
            // 1. Hapus gambar lama dari folder fisik jika ada
            if ($product->image != null) {
                $oldImagePath = public_path('uploads/products/' . $product->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Hapus file lama
                }
            }

            // 2. Upload gambar baru
            $image = $request->file('image');
            $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
            $image->move(public_path('uploads/products'), $imageName);
        }

        $purchasePrice = $request->purchase_price ?? $request->selling_price;

        // Update data ke database
        $product->update([
            'category_id'    => $request->category_id,
            'name'           => $request->name,
            'selling_price'  => $request->selling_price,
            'purchase_price' => $purchasePrice,
            'stock'          => $request->stock,
            'image'          => $imageName,
        ]);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    // Proses Menghapus Data (Delete)
    public function destroy(Product $product)
    {
        // Dengan SoftDeletes, data tidak dihapus permanen dari DB, 
        // melainkan kolom deleted_at diisi tanggal hari ini.
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus (masuk arsip)!');
    }
    // FITUR HAPUS TERPILIH (Bulk Delete)
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids; // Mengambil array ID dari checkbox yang dicentang

        if (!$ids || count($ids) == 0) {
            return redirect()->back()->with('error', 'Pilih minimal satu produk untuk dihapus.');
        }

        $products = Product::whereIn('id', $ids)->get();

        foreach ($products as $product) {
            // Hapus fisik gambar jika ada
            if (!empty($product->image)) {
                $imagePath = public_path('uploads/products/' . $product->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            // Hapus data dari DB
            $product->delete();
        }

        return redirect()->route('products.index')->with('success', count($ids) . ' produk terpilih berhasil dihapus!');
    }
}
