<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public function getConnectionName()
    {
        return $this->connection ?? config('database.default');
    }
    protected $fillable = [
        'cid',
        'name',
        'nik',
        'phone',
        'email',
        'address',
        'gps_lat',
        'gps_lng',
        'package_id',
        'ppn_scheme',
        'auth_method',
        'pppoe_user',
        'pppoe_password',
        'billing_type',
        'billing_cycle_type',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'gps_lat' => 'decimal:6',
        'gps_lng' => 'decimal:6',
        'expired_at' => 'datetime',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function radiusUser(): BelongsTo
    {
        return $this->belongsTo(RadiusUser::class, 'pppoe_user', 'username');
    }
}
