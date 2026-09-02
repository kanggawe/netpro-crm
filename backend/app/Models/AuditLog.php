<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'username',
        'action',
        'ip_address',
        'details',
        'status',
        'created_at',
    ];

    public static function log(string $username, string $action, ?string $details = null, string $status = 'success', ?string $ip = null): static
    {
        return static::create([
            'username' => $username,
            'action' => $action,
            'ip_address' => $ip ?? request()->ip() ?? '127.0.0.1',
            'details' => $details,
            'status' => $status,
            'created_at' => now(),
        ]);
    }
}
