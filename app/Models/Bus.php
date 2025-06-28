<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class Bus extends Model
{
    protected $fillable = [
        'phone',
        'latitude',
        'longitude',
        'address',
        'owner_name',
    ];
    


    
      public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
        
    }
}
