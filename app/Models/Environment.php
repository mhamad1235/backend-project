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
    protected $hidden = [ 'translations','updated_at','created_at'];


     protected $fillable = ['phone', 'latitude', 'longitude','city_id','type'];
     protected $casts = [
    'type' => RestaurantType::class,
    ];
     protected $appends = ['is_favorite','average_rating'];


     public static $hourlyPrices = [
        4 => 100,   
        8 => 180,   
        12 => 250,  
    ];

    public static $dailyPrice = 400; 
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
   
     public function favorites()
     {
      return $this->morphMany(Favorite::class, 'favoritable');
     }

    public function getIsFavoriteAttribute()
    {
       if (!auth()->check()) {
        return false;
       }

      return $this->favorites()->where('user_id', auth()->id())->exists();
    }
     
       public function properties()
      {
        return $this->morphToMany(Property::class, 'propertyable');
      }
  
}
