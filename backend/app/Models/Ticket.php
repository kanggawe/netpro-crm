<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_no',
        'customer_id',
        'category',
        'priority',
        'assigned_tech',
        'sla_minutes',
        'status',
        'description',
        'solution',
        'closed_at',
    ];

    protected $casts = [
        'sla_minutes' => 'integer',
        'closed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
