<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PoiController extends Controller
{
    /**
     * Get Points of Interest within a given bounding box from Overpass API.
     */
    public function index(Request $request)
    {
        $request->validate([
            'minLat' => 'required|numeric',
            'minLon' => 'required|numeric',
            'maxLat' => 'required|numeric',
            'maxLon' => 'required|numeric',
        ]);

        $minLat = $request->minLat;
        $minLon = $request->minLon;
        $maxLat = $request->maxLat;
        $maxLon = $request->maxLon;

        $client = new Client(['verify' => false]);

        $query = "[out:json];node({$minLat},{$minLon},{$maxLat},{$maxLon})['amenity'~'school|college|university|kindergarten|townhall|hospital|fire_station|police|restaurant|cafe|bar'];out;";

        try {
            $response = $client->post('https://overpass-api.de/api/interpreter', ['body' => $query]);
            $data = json_decode($response->getBody(), true);

            $pois = [];
            foreach ($data['elements'] as $element) {
                if (isset($element['tags']['name'])) {
                    $pois[] = [
                        'name' => $element['tags']['name'],
                        'type' => $element['tags']['amenity'] ?? 'unknown',
                        'lat' => $element['lat'],
                        'lon' => $element['lon'],
                    ];
                }
            }

            return response()->json($pois);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not fetch POIs from Overpass API.'], 500);
        }
    }
}