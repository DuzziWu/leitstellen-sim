<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\POI;

class ImportPois extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:pois {city}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Points of Interest (POIs) for a specific city from OpenStreetMap via Overpass API.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $city = $this->argument('city');

        if (empty($city)) {
            $this->error('Bitte gib einen Städtenamen an. Beispiel: php artisan import:pois "München"');
            return Command::FAILURE;
        }

        $this->info("Suche Koordinaten für {$city}...");
        $client = new Client([
            'verify' => false
        ]);
        
        try {
            // Schritt 1: Koordinaten der Stadt über die Nominatim API abrufen
            $nominatimResponse = $client->get('https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $city,
                    'format' => 'json',
                    'limit' => 1
                ],
                'headers' => [
                    'User-Agent' => 'Leitstellen-Simulator-App'
                ]
            ]);
            
            $locationData = json_decode($nominatimResponse->getBody(), true);

            if (empty($locationData)) {
                $this->error("Konnte keine Koordinaten für die Stadt '{$city}' finden.");
                return Command::FAILURE;
            }

            $latitude = $locationData[0]['lat'];
            $longitude = $locationData[0]['lon'];

            $this->info("Koordinaten gefunden: {$latitude}, {$longitude}.");
            $this->info("Starte POI-Import für verschiedene POI-Typen...");
            
            // Schritt 2: POIs im Radius über die Overpass API abfragen
            // Die Abfrage wurde erweitert, um mehrere POI-Typen zu finden.
            $query = "
                [out:json];
                (
                  node(around:5000, {$latitude}, {$longitude})[\"amenity\"~\"school|college|university|kindergarten|townhall|hospital|fire_station|police\"];
                  node(around:5000, {$latitude}, {$longitude})[\"shop\"];
                );
                out body;
                >;
                out skel qt;
            ";

            $overpassResponse = $client->post('https://overpass-api.de/api/interpreter', [
                'body' => $query,
                'headers' => [
                    'User-Agent' => 'Leitstellen-Simulator-App'
                ]
            ]);

            $data = json_decode($overpassResponse->getBody(), true);

            if (empty($data['elements'])) {
                $this->warn("Keine POIs im 5km-Radius von {$city} gefunden. Nichts zu importieren.");
                return Command::FAILURE;
            }

            $poisToImport = 0;
            foreach ($data['elements'] as $element) {
                if ($element['type'] !== 'node' || !isset($element['lat']) || !isset($element['lon'])) {
                    continue;
                }

                $name = $element['tags']['name'] ?? 'Unbekannt';
                $type = $element['tags']['amenity'] ?? ($element['tags']['shop'] ?? 'unknown');

                // Füge nur einen neuen POI hinzu, wenn er noch nicht existiert
                if (POI::where('latitude', $element['lat'])->where('longitude', $element['lon'])->exists()) {
                    continue;
                }
                
                POI::create([
                    'name' => $name,
                    'type' => $type,
                    'latitude' => $element['lat'],
                    'longitude' => $element['lon'],
                ]);

                $poisToImport++;
            }

            $this->info("Import erfolgreich! {$poisToImport} POIs für {$city} importiert.");
            return Command::SUCCESS;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->error('Verbindungsfehler zur API: ' . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Es ist ein Fehler aufgetreten: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}