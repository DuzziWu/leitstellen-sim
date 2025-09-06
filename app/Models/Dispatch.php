<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_type',
        'status',
        'lat',
        'lon',
        'reward',
        'user_id',
    ];

    protected $casts = [
        //
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}