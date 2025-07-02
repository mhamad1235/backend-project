<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabin extends Model
{
     protected $fillable = ['name', 'phone', 'latitude', 'longitude','city_id'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
     public function city()
    { 
    return $this->belongsTo(City::class);
    }
}
