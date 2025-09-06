<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name', 'image_path'];
    protected $hidden = ['created_at', 'updated_at','pivot'];
    public function hotels()
    {
        return $this->morphedByMany(Hotel::class, 'propertyable');
    }

    public function restaurants()
    {
        return $this->morphedByMany(Restaurant::class, 'propertyable');
    }

    public function environments()
    {
        return $this->morphedByMany(Environment::class, 'propertyable');
    }
}
