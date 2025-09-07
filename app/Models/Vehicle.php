<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'station_id',
        'user_id',
        'name',
        'price',
        'image',
        'stats',
        'status',
        'dispatch_id',
    ];

    // Füge dies hinzu, um das stats-Feld als JSON-Objekt zu speichern und zu lesen.
    protected $casts = [
        'stats' => 'array',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }
}