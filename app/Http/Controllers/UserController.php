<?php

// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class UserController extends Controller
{
    public function selectCity()
    {
        return view('cities.select');
    }

    public function saveCityByName(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|max:255',
        ]);

        // Geocoding-Dienst von OpenStreetMap (Nominatim) nutzen
        $response = Http::withHeaders([
            'User-Agent' => 'Leitstellen-Simulation'
        ])->withOptions(['verify' => false])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $request->city_name,
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1
        ]);


        if ($response->successful() && !empty($response->json())) {
            $location = $response->json()[0];
            $latitude = $location['lat'];
            $longitude = $location['lon'];

            $user = auth()->user();
            $user->city_latitude = $latitude;
            $user->city_longitude = $longitude;
            $user->save();

            return redirect()->route('dashboard')->with('success', 'Deine Stadt wurde erfolgreich gespeichert!');
        }

        return back()->withErrors(['city_name' => 'Stadt konnte nicht gefunden werden. Bitte versuche es erneut.']);
    }
}