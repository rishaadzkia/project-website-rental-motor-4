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
    Schema::table('bookings', function (Blueprint $table) {
        $table->enum('pickup_method', ['pool', 'delivery'])->default('pool');
        $table->text('pickup_address')->nullable();
        $table->integer('delivery_fee')->default(0);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn([
            'pickup_method',
            'pickup_address',
            'delivery_fee',
        ]);
    });
}
};
