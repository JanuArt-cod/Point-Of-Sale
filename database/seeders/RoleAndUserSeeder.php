<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat Role (Hak Akses)
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleKasir = Role::create(['name' => 'kasir']);

        // 2. Membuat Akun Admin
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@tes.id',
            'password' => Hash::make('admin'), // Hash::make untuk enkripsi password
        ]);
        // Tempelkan role admin ke akun ini
        $admin->assignRole($roleAdmin);

        // 3. Membuat Akun Kasir
        $kasir = User::create([
            'name' => 'Kasir Satu',
            'email' => 'kasir@tes.id',
            'password' => Hash::make('kasir'),
        ]);
        // Tempelkan role kasir ke akun ini
        $kasir->assignRole($roleKasir);
    }
}