<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'speed_mbps',
        'price',
        'default_ppn_mode',
        'category',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'speed_mbps' => 'integer',
        'is_active' => 'boolean',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
