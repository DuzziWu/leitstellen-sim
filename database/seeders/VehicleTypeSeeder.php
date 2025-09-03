<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VehicleType::create([
            'name' => 'Löschfahrzeug',
            'cost' => 50000,
            'personnel_required' => 9,
            'water_capacity' => 2400,
        ]);

        VehicleType::create([
            'name' => 'Rettungswagen',
            'cost' => 30000,
            'personnel_required' => 2,
        ]);

        VehicleType::create([
            'name' => 'Drehleiter',
            'cost' => 75000,
            'personnel_required' => 3,
        ]);
    }
}