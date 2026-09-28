<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. Aktifkan di sini
    
    protected $fillable = [
        'category_id', 'barcode', 'name', 
        'purchase_price', 'selling_price', 'stock', 'image'
    ];

    // Relasi: Produk milik 1 Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'product_id');
    }
}
