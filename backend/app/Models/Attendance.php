<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'employee_name',
        'division',
        'att_date',
        'shift_type',
        'clock_in',
        'clock_out',
        'gps_location',
        'gps_lat',
        'gps_lng',
        'status',
    ];

    protected $casts = [
        'att_date' => 'date',
        'gps_lat' => 'decimal:6',
        'gps_lng' => 'decimal:6',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
