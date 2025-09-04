<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mission;
use App\Models\Building;
use App\Models\MissionType;
use Illuminate\Support\Facades\Auth;


class MissionController extends Controller
{
    /**
     * Display a map with all active missions and stations.
     */
    public function index()
    {
        $user = Auth::user();
        $missions = Mission::where('status', 'pending')
            ->with('missionType')
            ->get();
        $buildings = Building::where('user_id', $user->id)->get();

        // Definiere hier den Radius für die Einsatzgenerierung
        $MISSION_RADIUS_KM = 3; 

        return view('missions.index', [
            'userLat' => $user->city_latitude,
            'userLon' => $user->city_longitude,
            'missions' => $missions,
            'buildings' => $buildings,
            'missionRadiusKm' => $MISSION_RADIUS_KM, // Übergeben Sie den Radius an die View
        ]);
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
        $radiusInKm = 3;

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