<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    protected $fillable = [
        'employee_id',
        'employee_name',
        'division',
        'position',
        'period',
        'tech_score',
        'discipline_score',
        'total_score',
        'notes',
        'supervisor_name',
    ];

    protected $casts = [
        'tech_score' => 'decimal:2',
        'discipline_score' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
