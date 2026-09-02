<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRecord extends Model
{
    protected $fillable = [
        'bupot_no',
        'tax_type',
        'vendor_name',
        'npwp',
        'obj_income',
        'dpp_amount',
        'rate_percent',
        'tax_amount',
        'period',
        'status',
        'ntpn',
    ];

    protected $casts = [
        'dpp_amount' => 'decimal:2',
        'rate_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];
}
