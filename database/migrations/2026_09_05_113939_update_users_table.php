<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp', 15)->nullable()->after('email');
            $table->string('no_ktp', 16)->unique()->nullable()->after('no_hp');
            $table->string('no_sim', 20)->nullable()->after('no_ktp');
            $table->text('alamat')->nullable()->after('no_sim');
            $table->enum('role', ['admin', 'customer'])->default('customer')->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'no_ktp', 'no_sim', 'alamat', 'role']);
        });
    }
};