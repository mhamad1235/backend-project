<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Restaurant extends Model implements TranslatableContract
{
    use HasFactory,Translatable;

    public $translatedAttributes = ['name', 'description'];
      protected $hidden = [ 'translations',"created_at", "updated_at"];

    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'city_id',
        'account_id', // Added to link to the account
    ];
    protected $appends = ['is_favorite'];
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
       return $this->morphMany(Image::class, 'imageable');
    }

    public function foods()
    {
      return $this->hasMany(Food::class);
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

      public function account()
     { 
     return $this->belongsTo(Account::class);
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
