<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'manager',
        'subs_count',
        'status',
    ];

    protected $casts = [
        'subs_count' => 'integer',
    ];
}
