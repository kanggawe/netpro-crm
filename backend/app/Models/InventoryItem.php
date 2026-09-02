<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'category',
        'stock',
        'min_stock',
        'unit',
        'unit_cost',
        'status',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'unit_cost' => 'decimal:2',
    ];
}
