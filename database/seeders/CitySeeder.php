<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Facades\Schema;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        City::truncate();
        Schema::enableForeignKeyConstraints();

        $csvFile = base_path('database/seeders/data/german_cities.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("Die CSV-Datei 'german_cities.csv' wurde nicht gefunden!");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        fgetcsv($file, 0, ';');

        while (($data = fgetcsv($file, 0, ';')) !== FALSE) {
            // Checken, ob die Zeile die erwartete Anzahl an Spalten hat (mindestens 20)
            if (count($data) < 20) {
                continue;
            }

            // Koordinaten aus der 20. Spalte (Index 19) holen
            $coordinates = explode(',', $data[19]);

            // Checken, ob die Koordinaten aus zwei Werten bestehen
            if (count($coordinates) < 2) {
                continue;
            }

            City::create([
                'name' => $data[1], // Der Name befindet sich in der 2. Spalte (Index 1)
                'lat' => trim($coordinates[0]),
                'lon' => trim($coordinates[1]),
            ]);
        }

        fclose($file);

        $this->command->info('Städtedaten erfolgreich importiert!');
    }
}