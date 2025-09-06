<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'type',
        'lat',
        'lon',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        // Wir stellen sicher, dass die Beziehung korrekt geladen wird
        return $this->hasMany(Vehicle::class);
    }
}