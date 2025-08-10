<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Hotel extends Model implements TranslatableContract
{
     use HasFactory,Translatable;
     protected $fillable = [
        'phone',
        'latitude',
        'longitude',
        'city_id',
    ];
     public $translatedAttributes = ['name', 'description'];
     protected $hidden = [ 'translations'];
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
    
     public function getAverageRatingAttribute()
     {
      $avg = $this->feedbacks()->avg('rating');
      return $avg ? number_format($avg, 1) : 0;
    }
  
     public function favorites()
      {
      return $this->morphMany(Favorite::class, 'favoritable');
      }

}
