<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RestaurantType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Environment extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name','description'];
    protected $with = ['translations'];


     protected $fillable = ['phone', 'latitude', 'longitude','city_id','type'];
     protected $casts = [
    'type' => RestaurantType::class,
    ];

     public static $hourlyPrices = [
        4 => 100,   // price for 4 hours
        8 => 180,   // price for 8 hours
        12 => 250,  // price for 12 hours
    ];

    public static $dailyPrice = 400; // price per full day (24h)
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
     public function city()
    { 
    return $this->belongsTo(City::class);
    }
    
    public function feedbacks()
    {
    return $this->morphMany(Feedback::class, 'feedbackable');
    }

    public function bookings()
    {
    return $this->morphMany(Booking::class, 'bookable');
    }

    public function unavailableSlots()
    {
    return $this->morphMany(UnavailableSlot::class, 'bookable');
    }
    
    public function getAverageRatingAttribute()
     {
      $avg = $this->feedbacks()->avg('rating');
      return $avg ? number_format($avg, 1) : 0;
    }
  
}
