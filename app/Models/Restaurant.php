<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'image',
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

}
