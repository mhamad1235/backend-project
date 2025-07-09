<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Bus extends Model implements TranslatableContract
{
    use HasFactory, Translatable;
    public $translatedAttributes = ['owner_name'];
    protected $with = ['translations'];
    protected $fillable = [
        'phone',
        'latitude',
        'longitude',
        'address',
        'city_id'
    ];
    


    
      public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
        
    }
    public function city()
    { 
    return $this->belongsTo(City::class);
    }

}
