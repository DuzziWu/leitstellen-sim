<?php

// app/Http/Controllers/BuildingController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\VehicleType;

class BuildingController extends Controller
{
    /**
     * Display a listing of the buildings.
     */
    public function index()
    {
        $buildings = auth()->user()->buildings;
        return view('buildings.index', compact('buildings'));
    }

    /**
     * Show the form for creating a new building.
     */
    public function create()
    {
        return view('buildings.create');
    }

    /**
     * Store a newly created building in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        auth()->user()->buildings()->create([
            'name' => $request->input('name'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        return redirect()->route('buildings.index');
    }

    /**
     * Display a specific building with its vehicles.
     */
    public function show(Building $building)
    {
        // Sicherstellen, dass der Benutzer der Besitzer der Wache ist
        if ($building->user_id !== auth()->id()) {
            abort(403);
        }

        $vehicleTypes = VehicleType::all();
        return view('buildings.show', compact('building', 'vehicleTypes'));
    }

    /**
     * Purchase a new vehicle for the building.
     */
    public function purchaseVehicle(Request $request, Building $building)
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
        ]);

        // Optional: Prüfen, ob der Spieler genug Geld hat und die Wache Platz hat

        // Neues Fahrzeug erstellen
        $building->vehicles()->create([
            'user_id' => auth()->id(),
            'vehicle_type_id' => $request->vehicle_type_id,
        ]);

        return redirect()->route('buildings.show', $building)->with('success', 'Fahrzeug erfolgreich gekauft!');
    }
    
}