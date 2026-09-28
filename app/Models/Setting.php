<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name', 'store_address', 'store_phone', 
        'store_logo', 'discount_threshold', 'discount_amount'
    ];
}