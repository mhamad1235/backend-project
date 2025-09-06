<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{   
    public $table = 'foods';
    protected $fillable = [
        'name', 'price', 'restaurant_id','description', 'is_available','category_id'
    ];
  protected $hidden = ['restaurant', 'translations',"created_at", "updated_at"];
    
     public function getPriceAttribute($value)
     {
    return (int) $value;
    }

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
      public function category()
    {
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }


}