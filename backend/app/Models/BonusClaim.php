<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusClaim extends Model
{
    protected $fillable = [
        'employee_id',
        'employee_name',
        'role',
        'bast_no',
        'points',
        'rate',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'points' => 'integer',
        'rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
