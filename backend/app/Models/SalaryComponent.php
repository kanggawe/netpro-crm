<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'formula',
        'borne_by',
        'status',
    ];
}
