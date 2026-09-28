<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'address'];

    // Relasi: 1 Customer bisa memiliki banyak Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
