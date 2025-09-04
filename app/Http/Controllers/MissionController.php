<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\MissionType;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    /**
     * Display a map with all active missions and stations.
     */
    public function index()
    {
        $user = auth()->user();
        $userLat = $user->city_latitude;
        $userLon = $user->city_longitude;

        $missions = Mission::with('missionType')->where('status', 'pending')->get();
        $buildings = auth()->user()->buildings;

        return view('missions.index', compact('missions', 'buildings', 'userLat', 'userLon'));
    }

    public function storeBuilding()
    {
        // Hier kommt die Logik für das Bauen der Wache hin.
        // Wir lassen sie erst einmal leer.
    }

    public function generateMission()
    {
        $user = auth()->user();
        $baseLat = $user->city_latitude;
        $baseLon = $user->city_longitude;

        // Radius in Kilometern, in dem Einsätze generiert werden sollen
        $radiusInKm = 5;

        // Zufällige Verschiebung für Längen- und Breitengrad berechnen
        $latOffset = ($radiusInKm / 111.3) * (rand(0, 200) - 100) / 100; // Ungefähre Umrechnung km zu Breitengraden
        $lonOffset = ($radiusInKm / (111.3 * cos(deg2rad($baseLat)))) * (rand(0, 200) - 100) / 100; // Ungefähre Umrechnung km zu Längengraden

        $newLat = $baseLat + $latOffset;
        $newLon = $baseLon + $lonOffset;
        
        // Zufälligen MissionType auswählen
        $missionTypes = MissionType::all();
        $randomMissionType = $missionTypes->random();

        // Neuen Einsatz erstellen
        Mission::create([
            'mission_type_id' => $randomMissionType->id,
            'latitude' => $newLat,
            'longitude' => $newLon,
            'status' => 'pending',
        ]);
        
        // Zurück zur Einsatzkarte mit einer Erfolgsmeldung
        return redirect()->back()->with('status', 'Einsatz erfolgreich generiert!');
    }
}