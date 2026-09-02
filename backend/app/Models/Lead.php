<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'package_interest',
        'sales_agent',
        'status',
        'notes',
    ];
}
