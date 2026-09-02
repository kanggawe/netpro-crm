<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'code',
        'title',
        'discount_amount',
        'quota',
        'used_count',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'quota' => 'integer',
        'used_count' => 'integer',
        'valid_until' => 'date',
    ];
}
