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
        // 1. Preserve existing statuses while adding 'pending_maintenance'
        DB::statement("ALTER TABLE motorcycles MODIFY COLUMN status ENUM('available', 'rented', 'maintenance', 'pending_maintenance') DEFAULT 'available'");

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
        // Map the new status to the closest status supported by the original enum.
        DB::table('motorcycles')
            ->where('status', 'pending_maintenance')
            ->update(['status' => 'maintenance']);

        // Restore the original enum values, including 'rented'.
        DB::statement("ALTER TABLE motorcycles MODIFY COLUMN status ENUM('available', 'rented', 'maintenance') DEFAULT 'available'");

        // Hapus kolom maintenance_requested_at jika ditambahkan
        Schema::table('motorcycles', function (Blueprint $table) {
            if (Schema::hasColumn('motorcycles', 'maintenance_requested_at')) {
                $table->dropColumn('maintenance_requested_at');
            }
        });
    }
};