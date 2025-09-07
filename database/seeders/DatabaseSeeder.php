<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        // Startguthaben vergeben
        $testUser->credits = 1000000;
        $testUser->save();

        $this->call([
            CitySeeder::class,
            StationSeeder::class, // Unsere neue Zeile hinzufügen
        ]);
    }
}
