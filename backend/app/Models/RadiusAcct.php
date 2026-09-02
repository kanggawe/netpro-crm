<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiusAcct extends Model
{
    protected $connection = 'radius';
    protected $table = 'radius_accts';

    protected $fillable = [
        'radacctid',
        'username',
        'nasipaddress',
        'framedipaddress',
        'acctstarttime',
        'acctstoptime',
        'acctsessiontime',
        'acctinputoctets',
        'acctoutputoctets',
        'acctterminatecause',
    ];

    protected $casts = [
        'acctstarttime' => 'datetime',
        'acctstoptime' => 'datetime',
        'acctsessiontime' => 'integer',
        'acctinputoctets' => 'integer',
        'acctoutputoctets' => 'integer',
    ];
}
