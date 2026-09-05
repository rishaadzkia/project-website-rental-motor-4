<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->string('kode_booking')->unique();
            
            // Foreign Keys
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('motor_id')->constrained('motors', 'motor_id')->onDelete('cascade');
            
            $table->dateTime('tgl_mulai');
            $table->dateTime('tgl_selesai');
            $table->integer('total_hari');
            $table->decimal('harga_per_hari', 10, 2);
            $table->decimal('diskon', 10, 2)->default(0);
            $table->decimal('total_harga', 10, 2);
            $table->enum('status_booking', [
                'belum_diproses', 
                'belum_diambil', 
                'sedang_dibawa', 
                'sudah_kembali', 
                'cancelled'
            ])->default('belum_diproses');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};