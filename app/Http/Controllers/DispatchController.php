<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Dispatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    /**
     * Generates a new dispatch based on the user's station assets and home city radius.
     */
    public function generateDispatch(): JsonResponse
    {
        $user = auth()->user();
        
        // Finde die Typen der Wachen, die der Benutzer besitzt
        $ownedStationTypes = Station::where('user_id', $user->id)
                                   ->pluck('type')
                                   ->unique()
                                   ->toArray();
        
        if (empty($ownedStationTypes)) {
            return response()->json(['error' => 'Keine Wache gefunden. Du musst zuerst eine Wache bauen, um Einsätze zu generieren.'], 400);
        }
        
        // Lade die Einsatzdaten aus der JSON-Datei
        $dispatchesJsonPath = public_path('data/dispatches.json');
        if (!file_exists($dispatchesJsonPath)) {
            return response()->json(['error' => 'Die Einsatzdaten-Datei wurde nicht gefunden.'], 500);
        }
        
        $dispatchesData = json_decode(file_get_contents($dispatchesJsonPath), true);
        
        // Filtere die Einsatzdaten, sodass nur Typen übrig bleiben, die zu den Wachen des Spielers passen
        $availableDispatches = collect($dispatchesData)->filter(function ($dispatch) use ($ownedStationTypes) {
            return in_array($dispatch['required_station_type'], $ownedStationTypes);
        })->values();

        if ($availableDispatches->isEmpty()) {
            return response()->json(['error' => 'Es konnten keine passenden Einsätze gefunden werden.'], 400);
        }

        // Wähle einen zufälligen Einsatz aus den gefilterten Daten
        $dispatchTypeData = $availableDispatches->random();
        
        // Generiere zufällige Koordinaten basierend auf der Heimatstadt des Benutzers
        $radiusInKm = 5; // Radius in Kilometern
        $lat = $user->home_city_lat + ($radiusInKm / 111.1) * (mt_rand(-100, 100) / 100);
        $lon = $user->home_city_lon + ($radiusInKm / (111.1 * cos(deg2rad($user->home_city_lat)))) * (mt_rand(-100, 100) / 100);

        // Erstelle den neuen Einsatz in der Datenbank
        $dispatch = new Dispatch([
            'dispatch_type' => $dispatchTypeData['id'],
            'lat' => $lat,
            'lon' => $lon,
            'reward' => $dispatchTypeData['reward'],
            'user_id' => $user->id,
        ]);
        $dispatch->save();
        
        return response()->json(['message' => 'Einsatz erfolgreich generiert!'], 200);
    }

    /**
     * Retrieves all active dispatches for the current user.
     */
    public function getActiveDispatches(): JsonResponse
    {
        $user = auth()->user();

        $dispatches = Dispatch::where('user_id', $user->id)
                                ->where('status', 'new') // Nur neue, noch nicht bearbeitete Einsätze
                                ->get();

        return response()->json($dispatches);
    }
}