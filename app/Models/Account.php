<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Enums\RoleType;
use App\Enums\AccountStatus;
class Account extends Authenticatable
{
    use HasApiTokens,Notifiable;

 protected $fillable = ['name', 'phone', 'password', 'role_type', 'status'];

     protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'role_type' => RoleType::class,
        'status' => AccountStatus::class,
    ];
    public function hotels()
    {
    return $this->hasMany(Hotel::class);
    }

    public function journeys()
    {
    return $this->hasMany(Journey::class, 'tourist_id');
    }
}
