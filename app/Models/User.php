<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions', // Wajib agar data hak akses bisa tersimpan ke database
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array', // Wajib agar format JSON dari database otomatis dibaca sebagai Array
    ];

    /**
     * Fungsi untuk mengecek apakah user memiliki hak akses ke menu tertentu
     */
    public function hasPermission($menu)
    {
        // 1. Jika role-nya Admin, otomatis berikan akses penuh ke semua menu
        if (strtolower($this->role) === 'admin') {
            return true;
        }
        
        // 2. Jika array permissions kosong atau null, tolak akses
        if (empty($this->permissions) || !is_array($this->permissions)) {
            return false;
        }

        // 3. Ubah semua isi array permissions menjadi huruf kecil agar aman dari perbedaan penulisan
        $permissions = array_map('strtolower', $this->permissions);

        // 4. Cek apakah menu yang direquest ada di dalam array permissions milik user
        return in_array(strtolower($menu), $permissions);
    }
}