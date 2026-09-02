<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = [
        'wo_no',
        'customer_name',
        'package_name',
        'ont_type',
        'ont_sn',
        'tech_name',
        'odp_port',
        'attenuation',
        'status',
        'bast_no',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}
