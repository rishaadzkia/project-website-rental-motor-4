<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('status');
            $table->string('driver_name')->nullable()->after('is_verified');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->text('admin_notes')->nullable()->after('driver_phone');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'driver_name', 'driver_phone', 'admin_notes']);
        });
    }
};