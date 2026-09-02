<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiusNas extends Model
{
    protected $connection = 'radius';
    protected $table = 'radius_nas';

    protected $fillable = [
        'nasname',
        'shortname',
        'type',
        'ports',
        'secret',
        'server',
        'community',
        'description',
        'ip_address',
        'api_port',
        'status',
    ];

    protected $casts = [
        'ports' => 'integer',
        'api_port' => 'integer',
    ];
}
