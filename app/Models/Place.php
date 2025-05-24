<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Place extends Model
{
    use Translatable;

    protected $fillable = ['image', 'latitude', 'longitude'];

    public $translatedAttributes = ['name', 'description'];
}
