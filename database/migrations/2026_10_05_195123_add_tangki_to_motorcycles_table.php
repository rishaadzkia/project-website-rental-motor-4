<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            // Menambahkan kolom kapasitas tangki (misal: 4.2 liter) setelah kolom 'image'
            $table->decimal('tank_capacity', 4, 1)->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('tank_capacity');
        });
    }
};