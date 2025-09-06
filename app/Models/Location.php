<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
      protected $fillable = ['latitude', 'longitude'];

    public function locatable()
    {
        return $this->morphTo();
    }
}
