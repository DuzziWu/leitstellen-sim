<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MissionType;

class MissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MissionType::create([
            'name' => 'Brandmeldeanlage',
            'required_vehicle_types' => json_encode(['Löschfahrzeug']),
            'min_vehicles' => 1,
            'max_vehicles' => 1,
            'reward' => 1500,
        ]);

        MissionType::create([
            'name' => 'Verkehrsunfall',
            'required_vehicle_types' => json_encode(['Löschfahrzeug', 'Rettungswagen']),
            'min_vehicles' => 2,
            'max_vehicles' => 3,
            'reward' => 5000,
        ]);

        MissionType::create([
            'name' => 'Wohnungsbrand',
            'required_vehicle_types' => json_encode(['Löschfahrzeug', 'Drehleiter', 'Rettungswagen']),
            'min_vehicles' => 3,
            'max_vehicles' => 5,
            'reward' => 12000,
        ]);
    }
}