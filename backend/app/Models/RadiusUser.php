<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RadiusUser extends Model
{
    protected $connection = 'radius';
    protected $table = 'radius_users';

    protected $fillable = [
        'username',
        'password',
        'customer_name',
        'profile_name',
        'ip_address',
        'nas_name',
        'mac_address',
        'rate_limit',
        'status',
        'last_online_at',
    ];

    protected $casts = [
        'last_online_at' => 'datetime',
    ];

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'pppoe_user', 'username');
    }
}
