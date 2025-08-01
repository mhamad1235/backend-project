<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Journey extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['name', 'description'];
    protected $with = ['translations'];
    protected $fillable = [
        'tourist_id',
        'duration',
        'price',
        'destination',
    ];
  
    public function tourist()
    {
        return $this->belongsTo(Account::class, 'tourist_id');
    }

      public function images()
{
    return $this->morphMany(Image::class, 'imageable');
}

}
