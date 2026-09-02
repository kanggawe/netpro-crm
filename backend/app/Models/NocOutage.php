<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NocOutage extends Model
{
    protected $fillable = [
        'outage_no',
        'location',
        'issue_type',
        'affected_users',
        'tech_name',
        'status',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'affected_users' => 'integer',
        'resolved_at' => 'datetime',
    ];
}
