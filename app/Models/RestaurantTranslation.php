<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTranslation extends Model
{
    public $timestamps = false; // optional
    protected $fillable = ['name', 'description'];
}
