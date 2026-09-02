<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'full_name',
        'email',
        'phone',
        'division',
        'role',
        'status',
        'password',
        'oauth_provider',
        'oauth_id',
        'avatar',
        'oauth_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'oauth_data' => 'array',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['super admin', 'superadmin']);
    }

    public function hasRole(string|array $roles): bool
    {
        if ($this->isSuperAdmin()) return true;
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles);
    }
}
