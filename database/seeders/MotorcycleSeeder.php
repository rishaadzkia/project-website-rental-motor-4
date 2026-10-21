<?php

namespace Database\Seeders;

use App\Models\Motorcycle;
use Illuminate\Database\Seeder;

class MotorcycleSeeder extends Seeder
{
    public function run(): void
    {
        $motorcycles = [
            [
                'name' => 'Honda Vario 160',
                'brand' => 'Honda',
                'model' => 'Vario 160 ABS',
                'plate_number' => 'B 1001 XYZ',
                'year' => 2024,
                'price_per_day' => 120000,
                'tank_capacity' => 5.5,
                'description' => 'Skuter matik 160 cc yang nyaman untuk perjalanan dalam kota.',
            ],
            [
                'name' => 'Honda Beat',
                'brand' => 'Honda',
                'model' => 'Beat Deluxe',
                'plate_number' => 'B 1002 XYZ',
                'year' => 2023,
                'price_per_day' => 80000,
                'tank_capacity' => 4.2,
                'description' => 'Skuter matik irit dan praktis untuk kebutuhan harian.',
            ],
            [
                'name' => 'Honda Scoopy',
                'brand' => 'Honda',
                'model' => 'Scoopy Stylish',
                'plate_number' => 'B 1003 XYZ',
                'year' => 2024,
                'price_per_day' => 95000,
                'tank_capacity' => 4.2,
                'description' => 'Skuter matik bergaya retro dengan posisi berkendara yang nyaman.',
            ],
            [
                'name' => 'Yamaha NMAX',
                'brand' => 'Yamaha',
                'model' => 'NMAX Connected',
                'plate_number' => 'B 1004 XYZ',
                'year' => 2024,
                'price_per_day' => 150000,
                'tank_capacity' => 7.1,
                'description' => 'Skuter matik premium yang nyaman untuk perjalanan jarak jauh.',
            ],
            [
                'name' => 'Yamaha Aerox',
                'brand' => 'Yamaha',
                'model' => 'Aerox 155 Connected',
                'plate_number' => 'B 1005 XYZ',
                'year' => 2023,
                'price_per_day' => 135000,
                'tank_capacity' => 5.5,
                'description' => 'Skuter matik 155 cc dengan desain sporty dan performa responsif.',
            ],
            [
                'name' => 'Yamaha Fazzio',
                'brand' => 'Yamaha',
                'model' => 'Fazzio Hybrid',
                'plate_number' => 'B 1006 XYZ',
                'year' => 2024,
                'price_per_day' => 100000,
                'tank_capacity' => 5.1,
                'description' => 'Skuter matik bergaya modern dengan teknologi hybrid.',
            ],
            [
                'name' => 'Suzuki Address',
                'brand' => 'Suzuki',
                'model' => 'Address FI',
                'plate_number' => 'B 1007 XYZ',
                'year' => 2023,
                'price_per_day' => 85000,
                'tank_capacity' => 5.2,
                'description' => 'Skuter matik ringan dengan ruang penyimpanan yang praktis.',
            ],
            [
                'name' => 'Honda PCX 160',
                'brand' => 'Honda',
                'model' => 'PCX 160 ABS',
                'plate_number' => 'B 1008 XYZ',
                'year' => 2025,
                'price_per_day' => 160000,
                'tank_capacity' => 8.1,
                'description' => 'Skuter matik premium dengan posisi duduk nyaman dan bagasi luas.',
            ],
            [
                'name' => 'Yamaha Mio M3',
                'brand' => 'Yamaha',
                'model' => 'Mio M3 125',
                'plate_number' => 'B 1009 XYZ',
                'year' => 2023,
                'price_per_day' => 80000,
                'tank_capacity' => 4.2,
                'description' => 'Skuter matik 125 cc yang lincah dan cocok untuk penggunaan harian.',
            ],
            [
                'name' => 'Honda Genio',
                'brand' => 'Honda',
                'model' => 'Genio CBS',
                'plate_number' => 'B 1010 XYZ',
                'year' => 2024,
                'price_per_day' => 90000,
                'tank_capacity' => 4.2,
                'description' => 'Skuter matik ringan dengan desain kasual untuk mobilitas sehari-hari.',
            ],
        ];

        foreach ($motorcycles as $motorcycle) {
            Motorcycle::updateOrCreate(
                ['plate_number' => $motorcycle['plate_number']],
                [...$motorcycle, 'status' => 'available']
            );
        }
    }
}
