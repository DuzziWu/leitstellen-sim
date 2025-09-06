<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Dispatch;
use App\Models\Vehicle; // Hinzugefügt
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    /**
     * Generiert einen neuen Einsatz basierend auf den Wachen des Benutzers.
     * Methode wurde in 'generate' umbenannt, um zur Route zu passen.
     */
    public function generate(Request $request): JsonResponse
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
            'status' => 'active', // <--- DIESE ZEILE WURDE HINZUGEFÜGT
        ]);
        $dispatch->save();

        return response()->json(['message' => 'Einsatz erfolgreich generiert!'], 200);
    }

    /**
     * Ruft alle aktiven Einsätze für den aktuellen Benutzer ab.
     */
    public function getActiveDispatches(): JsonResponse
    {
        $user = auth()->user();

        $dispatches = Dispatch::where('user_id', $user->id)
            ->where('status', 'active') // Nur aktive Einsätze abrufen
            ->get();

        return response()->json($dispatches);
    }

    /**
     * Alarmiert die ausgewählten Fahrzeuge zu einem Einsatz.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\JsonResponse
     */
    public function alertVehicles(Request $request, Dispatch $dispatch)
    {
        // Validiere die Eingabe: vehicle_ids muss ein Array sein
        $request->validate([
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
        ]);

        // Konvertiere die Fahrzeug-IDs zur Sicherheit in Integers
        $vehicleIds = array_map('intval', $request->input('vehicle_ids'));

        $userId = Auth::id();

        // Überprüfe, ob die Fahrzeuge dem angemeldeten Benutzer gehören
        $userVehicles = Vehicle::whereIn('id', $vehicleIds)
            ->where('user_id', $userId)
            ->get();

        if ($userVehicles->count() !== count($vehicleIds)) {
            return response()->json(['error' => 'Ein oder mehrere Fahrzeuge konnten nicht gefunden werden oder gehören nicht dir.'], 403);
        }

        // Überprüfe, ob der Einsatz dem Benutzer gehört und aktiv ist
        if ((int) $dispatch->user_id !== (int) $userId || $dispatch->status !== 'active') {
            return response()->json(['error' => 'Dieser Einsatz ist nicht verfügbar.'], 403);
        }

        // Weist die Fahrzeuge dem Einsatz zu und ändert ihren Status
        foreach ($userVehicles as $vehicle) {
            $vehicle->dispatch_id = $dispatch->id;
            $vehicle->status = 'en_route';
            $vehicle->save();
        }

        // Ändere den Status des Einsatzes auf "in_progress", wenn mindestens ein Fahrzeug zugewiesen wurde
        if ($userVehicles->count() > 0) {
            $dispatch->status = 'in_progress';
            $dispatch->save();
        }

        return response()->json(['message' => 'Fahrzeuge erfolgreich alarmiert!']);
    }
}