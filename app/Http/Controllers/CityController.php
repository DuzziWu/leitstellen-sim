<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        return view('city-selection');
    }

    // In app/Http/Controllers/CityController.php
    public function store(Request $request)
    {
        $request->validate([
            'home_city' => 'required|string|max:255',
        ]);

        $city = City::where('name', $request->input('home_city'))->first();

        if (!$city) {
            return redirect()->back()->withErrors(['home_city' => 'Diese Stadt wurde nicht in unserer Datenbank gefunden. Bitte versuche es erneut.']);
        }

        $user = Auth::user();
        $user->home_city_lat = $city->lat;
        $user->home_city_lon = $city->lon;
        $user->save();

        return redirect()->route('dashboard');
    }
}