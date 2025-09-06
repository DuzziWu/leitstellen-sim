<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CombinedRegisterController extends Controller
{
    public function showForm()
    {
        return view('combined-register');
    }

    public function processForm(Request $request)
    {
        // Validierungsregel von 'username' zu 'name' ändern
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'home_city' => ['required', 'string', 'max:255', Rule::exists('cities', 'name')],
        ]);

        // Benutzereingabe von 'username' zu 'name' ändern
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Stadt-Daten speichern
        $city = City::where('name', $request->input('home_city'))->first();
        $user->home_city_lat = $city->lat;
        $user->home_city_lon = $city->lon;
        $user->save();

        // Benutzer einloggen und zum Dashboard weiterleiten
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}