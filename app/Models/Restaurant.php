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
    protected $with = ['translations'];

    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'city_id',
    ];

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
}  
