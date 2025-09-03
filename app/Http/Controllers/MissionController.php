<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    /**
     * Display a map with all active missions.
     */
    public function index()
    {
        $missions = Mission::where('status', 'pending')->get();
        return view('missions.index', compact('missions'));
    }
}