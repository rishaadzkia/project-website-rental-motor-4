<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update enum kolom 'status' untuk menambah 'pending_maintenance'
        DB::statement("ALTER TABLE motorcycles MODIFY COLUMN status ENUM('available', 'maintenance', 'pending_maintenance') DEFAULT 'available'");

        // 2. Tambahkan kolom 'maintenance_requested_at' jika belum ada
        Schema::table('motorcycles', function (Blueprint $table) {
            if (!Schema::hasColumn('motorcycles', 'maintenance_requested_at')) {
                $table->timestamp('maintenance_requested_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom status ke enum semula
        DB::statement("ALTER TABLE motorcycles MODIFY COLUMN status ENUM('available', 'maintenance') DEFAULT 'available'");

        // Hapus kolom maintenance_requested_at jika ditambahkan
        Schema::table('motorcycles', function (Blueprint $table) {
            if (Schema::hasColumn('motorcycles', 'maintenance_requested_at')) {
                $table->dropColumn('maintenance_requested_at');
            }
        });
    }
};