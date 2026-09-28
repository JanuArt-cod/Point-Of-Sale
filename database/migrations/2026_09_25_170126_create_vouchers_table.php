<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: PROMOJUMAT
            $table->string('type'); // 'nominal' (potongan langsung Rp) atau 'percent' (potongan persen %)
            $table->decimal('value', 15, 2); // Nilai nominal atau persentase
            $table->date('valid_until')->nullable(); // Masa berlaku (opsional)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};