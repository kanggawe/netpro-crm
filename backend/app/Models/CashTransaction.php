<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'trans_date',
        'description',
        'bank_account',
        'type',
        'amount',
        'status',
    ];

    protected $casts = [
        'trans_date' => 'date',
        'amount' => 'decimal:2',
    ];
}
