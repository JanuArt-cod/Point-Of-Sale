<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'valid_until', 'is_active'];

    public function transactions()
{
    return $this->hasMany(Transaction::class);
}
}

