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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'password' => 'hashed',
        'role_type' => RoleType::class,
        'status' => AccountStatus::class,
    ];
    public function hotel()
    {
    return $this->hasOne(Hotel::class);
    }

    public function journeys()
    {
    return $this->hasMany(Journey::class, 'tourist_id');
    }
    
    public function restaurant()
    {
    return $this->hasOne(Restaurant::class);
    }

}
