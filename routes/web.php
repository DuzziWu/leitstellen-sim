<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\UserController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/buildings', [BuildingController::class, 'index'])->name('buildings.index');
    Route::get('/buildings/create', [BuildingController::class, 'create'])->name('buildings.create');
    Route::post('/buildings', [BuildingController::class, 'store'])->name('buildings.store');
    Route::get('/buildings/{building}', [BuildingController::class, 'show'])->name('buildings.show');
    Route::post('/buildings/{building}/purchase', [BuildingController::class, 'purchaseVehicle'])->name('buildings.purchase-vehicle');
    Route::get('/missions', [MissionController::class, 'index'])->name('missions.index');
    Route::get('/select-city', [UserController::class, 'selectCity'])->name('user.select_city');
    Route::post('/save-city', [UserController::class, 'saveCityByName'])->name('user.save_city_name');
    Route::post('/buildings/store', [MissionController::class, 'storeBuilding'])->name('buildings.store.from_map');
    Route::post('/missions/generate', [MissionController::class, 'generateMission'])->name('missions.generate');

    
});

require __DIR__.'/auth.php';