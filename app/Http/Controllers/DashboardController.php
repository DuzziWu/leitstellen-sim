<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Station; // <-- Diese Zeile ist entscheidend

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user) {
            $station = Station::where('user_id', $user->id)->first();
            return view('dashboard', ['station' => $station]);
        }
        
        return view('dashboard', ['station' => null]);
    }
}