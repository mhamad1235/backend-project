<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabin extends Model
{
     protected $fillable = ['name', 'phone', 'latitude', 'longitude'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
