<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motors', function (Blueprint $table) {
            $table->id('motor_id');
            $table->string('nama_motor');
            $table->string('merk');
            $table->string('tipe');
            $table->decimal('kapasitas_tangki', 4, 1)->nullable();
            $table->string('plat_nomor')->unique();
            $table->text('deskripsi')->nullable();
            $table->text('perlengkapan')->nullable();
            $table->string('gambar_url')->nullable();
            $table->decimal('harga_per_hari', 10, 2);
            $table->enum('status', ['tersedia', 'disewakan', 'perawatan'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motors');
    }
};