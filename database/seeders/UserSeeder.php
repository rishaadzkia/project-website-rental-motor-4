<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['no_hp' => '081234567890'],
            [
                'name'     => 'Admin Rental',
                'no_hp'    => '081234567890',
                'no_ktp'   => '1111111111111111',
                'no_sim'   => '1111111111111111',
                'email'    => 'admin@gmail.com',
                'alamat'   => 'Kantor Pusat Rental Motor',
                'password' => '12345678', 
                'role'     => 'admin',
            ]
        );

        // 2. Akun Customer (Sampel untuk Testing)
        User::updateOrCreate(
            ['no_hp' => '089876543210'],
            [
                'name'     => 'Customer Sampel',
                'no_hp'    => '089876543210',
                'no_ktp'   => '3171019876540002',
                'no_sim'   => '987654321098',
                'email'    => 'customer@gmail.com',
                'alamat'   => 'Jl. Testing No. 12',
                'password' => 'customer123',
                'role'     => 'customer',
            ]
        );
    }
}