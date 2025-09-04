<?php

// app/Models/FireStation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FireStation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'latitude', 'longitude'];
}
