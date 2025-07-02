<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    public $table = 'foods';
    protected $fillable = [
        'name', 'price', 'category', 'restaurant_id'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

  public function images()
{
    return $this->morphMany(Image::class, 'imageable');
}

}

