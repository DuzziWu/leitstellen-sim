<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportStations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:stations {state}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import fire stations for a given state code (e.g., DE-BB)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $stateCode = $this->argument('state');
        $this->info("Starte Overpass-Abfrage für Bundesland: {$stateCode}...");

        $client = new Client([
            // Fügt einen User-Agent-Header für alle Anfragen hinzu
            'headers' => ['User-Agent' => 'LeitstellenSim/1.0 (dein-email@example.com)'],
        ]);
        
        // Dynamische Overpass-Abfrage
        $query = "[out:json][timeout:250];area[\"ISO3166-2\"=\"{$stateCode}\"]->.area;(node(area.area)[\"amenity\"=\"fire_station\"];way(area.area)[\"amenity\"=\"fire_station\"];);out body;>;out skel qt;";

        try {
            $response = $client->post('https://overpass-api.de/api/interpreter', [
                'form_params' => [
                    'data' => $query,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $importedCount = 0;
            $skippedCount = 0;
            $geocodedCount = 0;

            $this->info('Daten erfolgreich von Overpass empfangen. Starte Import...');

            foreach ($data['elements'] as $element) {
                if (!in_array($element['type'], ['node', 'way'])) {
                    $this->warn("Überspringe Element-Typ '{$element['type']}'");
                    $skippedCount++;
                    continue;
                }

                $name = $element['tags']['name'] ?? $element['tags']['alt_name'] ?? null;
                if (!is_string($name) || empty(trim($name))) {
                    $skippedCount++;
                    continue;
                }
                
                $address = $element['tags']['addr:street'] ?? 'unbekannt';
                if (!is_string($address)) {
                    $address = 'unbekannt';
                }

                $lat = $element['lat'] ?? null;
                $lon = $element['lon'] ?? null;

                if ($element['type'] === 'way' && isset($element['bounds'])) {
                    $lat = ($element['bounds']['minlat'] + $element['bounds']['maxlat']) / 2;
                    $lon = ($element['bounds']['minlon'] + $element['bounds']['maxlon']) / 2;
                }
                
                if ($lat === null || $lon === null) {
                    $fullAddress = trim(($element['tags']['addr:street'] ?? '') . ' ' . ($element['tags']['addr:housenumber'] ?? '') . ' ' . ($element['tags']['addr:city'] ?? ''));
                    if ($fullAddress !== '' && $fullAddress !== 'unbekannt') {
                        try {
                            $geoResponse = $client->get("https://nominatim.openstreetmap.org/search?q=" . urlencode($fullAddress) . "&format=json&limit=1");
                            $geoData = json_decode($geoResponse->getBody(), true);
                            if (isset($geoData[0])) {
                                $lat = $geoData[0]['lat'];
                                $lon = $geoData[0]['lon'];
                                $geocodedCount++;
                            }
                        } catch (\Exception $e) {
                            $this->warn("Geocoding-Fehler für '{$name}': " . $e->getMessage());
                        }
                    }
                }
                
                if ($lat && $lon) {
                    try {
                        DB::table('stations')->insert([
                            'name' => $name,
                            'type' => 'Feuerwehr',
                            'address' => $address,
                            'lat' => $lat,
                            'lon' => $lon,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $importedCount++;
                    } catch (\Exception $e) {
                        $this->error("Fehler beim Import von '{$name}' (ID: {$element['id']}): " . $e->getMessage());
                        $skippedCount++;
                    }
                } else {
                    $this->warn("Überspringe Wache '{$name}' (ID: {$element['id']}) wegen fehlender Koordinaten und keiner Adresse.");
                    $skippedCount++;
                }
            }
            $this->info("Seeding abgeschlossen. {$importedCount} Wachen importiert ({$geocodedCount} per Geocoding). {$skippedCount} übersprungen.");

        } catch (\Exception $e) {
            $this->error("Fehler bei der Overpass-Abfrage: " . $e->getMessage());
        }
    }
}