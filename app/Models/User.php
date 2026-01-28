<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
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
        ];
    }

    public function managedBranch()
    {
        return $this->hasOne(Branch::class, 'managerID');
    }

    public function getBranchAttribute()
    {
        return $this->managedBranch;
    }

    public function managedStores()
    {
        return $this->hasMany(Store::class, 'managerID');
    }

    public function requestedTransfers()
    {
        return $this->hasMany(Transfer::class, 'requestedBy');
    }

    public function approvedTransfers()
    {
        return $this->hasMany(Transfer::class, 'approvedBy');
    }
}
