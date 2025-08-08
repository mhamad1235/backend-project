<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    public $table = 'foods';
    protected $fillable = [
        'name', 'price', 'category', 'restaurant_id','description', 'is_available'
    ];
  protected $hidden = ['restaurant', 'translations'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function images()
    {  
    return $this->morphMany(Image::class, 'imageable');
    }
  

    public function scopeAvailable($query)
    {
    return $query->where('is_available', true);
    }


}