<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\FireStation; // Dieses Model wird verwendet

class ImportStations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:stations {city} {--source=osm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importiert Feuer-, Rettungs- und Polizeiwachen für eine gegebene Stadt.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $city = $this->argument('city');

        $this->info("Importiere Wachen für die Stadt: " . $city);

        $query = '[out:json][timeout:25];
        area["name"="' . $city . '"]->.searchArea;
        (
        node["amenity"="fire_station"](area.searchArea);
        node["amenity"="hospital"](area.searchArea);
        node["amenity"="police"](area.searchArea);
        node["emergency"="ambulance_station"](area.searchArea);
        node["name"~"Feuerwehr", i](area.searchArea);
        node["name"~"Polizei", i](area.searchArea);
        node["name"~"Rettungsdienst|Rettungswache", i](area.searchArea);
        );
        out body;
        >;
        out skel qt;';

        $response = Http::withoutVerifying()->get('https://overpass-api.de/api/interpreter', [
            'data' => $query
        ]);

        if ($response->successful()) {
            $elements = $response->json()['elements'];

            foreach ($elements as $element) {
                if ($element['type'] === 'node' && isset($element['tags']['name'])) {
                    $name = $element['tags']['name'];
                    $type = null;

                    if (str_contains($name, 'Feuerwehr')) {
                        $type = 'Feuerwache';
                    } elseif (str_contains($name, 'Polizei')) {
                        $type = 'Polizeiwache';
                    } elseif (str_contains($name, 'Rettung') || str_contains($name, 'Krankenhaus') || str_contains($name, 'Rettungsdienst')) {
                        $type = 'Rettungswache';
                    }
                    
                    $existingStation = FireStation::where('latitude', $element['lat'])
                                                ->where('longitude', $element['lon'])
                                                ->first();

                    if (!$existingStation) {
                        FireStation::create([
                            'name' => $name,
                            'type' => $type,
                            'latitude' => $element['lat'],
                            'longitude' => $element['lon'],
                        ]);
                    }
                }
            }
            $this->info("Import abgeschlossen!");
        } else {
            $this->error("Fehler beim Abrufen der Daten von der Overpass API.");
        }
    }
}