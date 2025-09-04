<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FireStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [
            ['name' => 'Feuerwache Mitte', 'latitude' => 52.5222, 'longitude' => 13.4079],
            ['name' => 'Feuerwache Kreuzberg', 'latitude' => 52.5029, 'longitude' => 13.3934],
            ['name' => 'Feuerwache Neukölln', 'latitude' => 52.4839, 'longitude' => 13.4357],
            ['name' => 'Feuerwache Charlottenburg', 'latitude' => 52.5152, 'longitude' => 13.2848],
            ['name' => 'Feuerwache Prenzlauer Berg', 'latitude' => 52.5447, 'longitude' => 13.4216],
        ];

        foreach ($stations as $station) {
            DB::table('fire_stations')->insert($station);
        }
    }
}