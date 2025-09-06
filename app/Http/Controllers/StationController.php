<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StationController extends Controller
{
    /**
     * Get stations within a given bounding box.
     */
    public function index(Request $request): JsonResponse
    {
        $minLat = $request->query('minLat');
        $minLon = $request->query('minLon');
        $maxLat = $request->query('maxLat');
        $maxLon = $request->query('maxLon');

        $userId = Auth::id();

        if ($minLat === null || $minLon === null || $maxLat === null || $maxLon === null) {
            return response()->json(['error' => 'Missing bounding box parameters'], 400);
        }

        $stations = Station::whereBetween('lat', [$minLat, $maxLat])
            ->whereBetween('lon', [$minLon, $maxLon])
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $userId);
            })
            ->get();

        return response()->json($stations);
    }

    /**
     * Buy a station.
     */
    public function buy(Request $request, Station $station)
    {
        $user = auth()->user();

        if ($station->user_id !== null) {
            return back()->with('error', 'Diese Wache wurde bereits gekauft.');
        }

        $station->user_id = $user->id;
        $station->save();

        return back()->with('success', 'Wache erfolgreich gekauft!');
    }

    /**
     * Buy a vehicle for a station.
     */
    public function buyVehicle(Request $request, Station $station): JsonResponse
    {
        $vehiclesJsonPath = public_path('data/vehicles_fire.json');

        if (!file_exists($vehiclesJsonPath)) {
            return response()->json(['error' => 'Die Fahrzeugdaten-Datei wurde nicht gefunden.'], 500);
        }

        $vehiclesData = json_decode(file_get_contents($vehiclesJsonPath), true);

        // Finde die Details des ausgewählten Fahrzeugtyps
        $selectedVehicleData = collect($vehiclesData)->firstWhere('id', $request->vehicle_type);

        if (!$selectedVehicleData) {
            return response()->json(['error' => 'Fahrzeugtyp nicht gefunden.'], 404);
        }

        // Stelle sicher, dass die Wache freie Stellplätze hat
        $vehicleCount = $station->vehicles()->count();
        if ($vehicleCount >= 4) {
            return response()->json(['error' => 'Kein freier Stellplatz vorhanden.'], 400);
        }

        // Neues Fahrzeug erstellen und in der Datenbank speichern
        $vehicle = new Vehicle([
            'vehicle_type' => $selectedVehicleData['id'],
            'station_id' => $station->id,
            'user_id' => Auth::id(),
        ]);
        $vehicle->save();

        return response()->json(['message' => 'Fahrzeug erfolgreich gekauft!'], 200);
    }

    /**
     * Get vehicles for a specific station, merging details from a JSON file.
     */
    public function getVehicles(Station $station): JsonResponse
    {
        // Holt die Fahrzeuge direkt über eine Abfrage
        $vehicles = Vehicle::where('station_id', $station->id)->get();

        // Lese die Fahrzeugdaten aus der JSON-Datei
        $vehiclesJson = json_decode(file_get_contents(public_path('data/vehicles_fire.json')), true);

        // Füge die Details aus der JSON-Datei den Datenbank-Fahrzeugen hinzu
        $vehiclesWithDetails = $vehicles->map(function ($vehicle) use ($vehiclesJson) {
            $details = collect($vehiclesJson)->firstWhere('id', $vehicle->vehicle_type);
            if ($details) {
                // Kombiniere die DB-Daten mit den JSON-Details
                return array_merge($vehicle->toArray(), $details);
            }
            return $vehicle;
        });

        return response()->json($vehiclesWithDetails);
    }

    /**
     * Delete a vehicle from a station.
     */
    public function deleteVehicle(Request $request, Station $station, Vehicle $vehicle): JsonResponse
    {
        // Sicherstellen, dass das Fahrzeug zur Wache gehört
        if ($vehicle->station_id !== $station->id) {
            return response()->json(['error' => 'Fahrzeug gehört nicht zu dieser Wache.'], 403);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Fahrzeug erfolgreich gelöscht.'], 200);
    }

    public function getPlayerStationsWithVehicles()
    {
        $stations = Station::with('vehicles')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($stations);
    }
}