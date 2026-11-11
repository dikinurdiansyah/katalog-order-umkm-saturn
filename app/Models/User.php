<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'phone_number',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isOwner(): bool
    {
        return $this->role === 'OWNER';
    }
}
