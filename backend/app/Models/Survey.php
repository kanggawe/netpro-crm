<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'survey_no',
        'customer_name',
        'phone',
        'address',
        'gps_lat',
        'gps_lng',
        'nearest_odp',
        'distance_m',
        'tech_name',
        'status',
        'attenuation',
        'notes',
    ];

    protected $casts = [
        'gps_lat' => 'decimal:6',
        'gps_lng' => 'decimal:6',
        'distance_m' => 'integer',
    ];
}
