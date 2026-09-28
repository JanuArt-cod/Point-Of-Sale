<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'user_id', 'customer_id', 'voucher_id',
        'subtotal', 'discount', 'grand_total',
        'paid_amount', 'change_amount', 'payment_method'
    ];

    // Relasi: Transaksi memiliki banyak Detail Rincian
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relasi: Transaksi dilayani oleh 1 User/Kasir
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // TAMBAHKAN INI: Relasi transaksi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}