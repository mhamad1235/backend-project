<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Meal;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable implements MustVerifyEmail
{

    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'dob',
        'fcm',
        'password',
        'phone',
        'city_id'

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

    public function city()
    {
        return $this->belongsTo(City::class)->withDefault([
            "name" => "N/A",
        ]);
    }

   
     public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteRestaurants()
    {
    return $this->morphedByMany(Restaurant::class, 'favoritable', 'favorites');
    }


    public function ssos()
    {
        return $this->hasMany(Sso::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    
    public function busBookings(): HasMany
    {
        return $this->hasMany(Booking::class)
            ->where('bookable_type', Bus::class);
    }
    
    public function journeys()
    {
    return $this->belongsToMany(Journey::class)->withPivot('paid')->withTimestamps();
    }
    public function journeyUsers()
    {
    return $this->hasMany(JourneyUser::class);
    } 

     public function notifications(): MorphMany
    {
    return $this->morphMany(Notification::class, 'notifiable');
    }

    
 

}
