<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('POS APP STORE');
            $table->text('store_address')->nullable();
            $table->string('store_phone')->nullable();
            $table->string('store_logo')->nullable(); // Untuk menyimpan nama file logo
            $table->integer('discount_threshold')->default(100000); // Syarat belanja (misal > 100rb)
            $table->integer('discount_amount')->default(10000);     // Potongan harga (misal 10rb)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};