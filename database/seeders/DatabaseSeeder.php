<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil file RoleAndUserSeeder yang baru saja kita buat
        $this->call([
            RoleAndUserSeeder::class,
        ]);
    }
}