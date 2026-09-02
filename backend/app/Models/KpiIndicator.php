<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiIndicator extends Model
{
    protected $fillable = [
        'division',
        'name',
        'target',
        'weight',
        'method',
        'status',
    ];

    protected $casts = [
        'weight' => 'integer',
    ];
}
