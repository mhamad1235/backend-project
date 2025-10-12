<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Place extends Model
{
    use Translatable;

    protected $fillable = ['latitude', 'longitude','city_id'];

    public $translatedAttributes = ['name', 'description'];
    protected $hidden=['translations'];
     protected $appends = ['is_favorite','average_rating'];

      public function city()
    { 
    return $this->belongsTo(City::class);
    }
      public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
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

    public function getIsFavoriteAttribute()
    {
       if (!auth()->check()) {
        return false;
       }

      return $this->favorites()->where('user_id', auth()->id())->exists();
    }
     
}
