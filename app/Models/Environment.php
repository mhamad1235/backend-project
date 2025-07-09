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
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
     public function city()
    { 
    return $this->belongsTo(City::class);
    }
}
