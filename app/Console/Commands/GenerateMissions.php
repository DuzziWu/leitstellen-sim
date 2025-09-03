<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mission;
use App\Models\MissionType;
use Illuminate\Support\Facades\DB;

class GenerateMissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missions:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates new missions for the game.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $missionTypes = MissionType::all();

        if ($missionTypes->isEmpty()) {
            $this->error('No mission types found. Please run the MissionTypeSeeder.');
            return;
        }

        // Zufälligen Einsatztyp auswählen
        $randomMissionType = $missionTypes->random();

        // Zufällige Position generieren (innerhalb eines bestimmten Radius)
        $latitude = 52.5200 + (rand(-100, 100) / 10000);
        $longitude = 13.4050 + (rand(-100, 100) / 10000);

        // Neuen Einsatz erstellen
        Mission::create([
            'mission_type_id' => $randomMissionType->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $this->info('New mission generated: ' . $randomMissionType->name);
    }
}