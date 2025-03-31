<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthData extends Model
{
    /** @use HasFactory<\Database\Factories\HealthDataFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'steps', 'distance_km', 'active_minutes'];
}
