<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('pickup_time')->nullable()->after('end_date');
            $table->time('return_time')->nullable()->after('pickup_time');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('actual_pickup')->nullable()->after('paid_at');
            $table->timestamp('actual_return')->nullable()->after('actual_pickup');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_time',
                'return_time',
                'paid_at',
                'actual_pickup',
                'actual_return',
            ]);
        });
    }
};