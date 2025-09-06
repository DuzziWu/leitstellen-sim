<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CombinedRegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\DispatchController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Unsere Routen für das Spiel
    Route::get('/select-city', [CityController::class, 'index'])->name('city.selection');
    Route::post('/select-city', [CityController::class, 'store'])->name('city.store');

    Route::get('/register-flow', [CombinedRegisterController::class, 'showForm'])->name('register.flow');
    Route::post('/register-flow', [CombinedRegisterController::class, 'processForm']);

    // Routen zum Abrufen und Kaufen von Wachen
    Route::get('/api/stations', [StationController::class, 'index'])->name('api.stations');
    Route::post('/stations/{station}/buy', [StationController::class, 'buy'])->name('stations.buy');

    // Routen für Fahrzeuge und Einsätze
    Route::post('/stations/{station}/buy-vehicle', [StationController::class, 'buyVehicle'])->name('stations.buyVehicle');
    Route::get('/api/stations/{station}/vehicles', [StationController::class, 'getVehicles']);
    Route::delete('/stations/{station}/vehicles/{vehicle}', [App\Http\Controllers\StationController::class, 'deleteVehicle']);

    // Routen für die Einsatzgenerierung und -anzeige
    Route::post('/dispatches/generate', [App\Http\Controllers\DispatchController::class, 'generateDispatch']);
    Route::get('/api/dispatches', [DispatchController::class, 'getActiveDispatches']);
});


require __DIR__.'/auth.php';