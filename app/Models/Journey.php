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
     protected $appends = ['is_favorite'];
  
    public function tourist()
    {
        return $this->belongsTo(Account::class, 'tourist_id');
    }

    public function images()
    {
    return $this->morphMany(Image::class, 'imageable');
    }

    public function users()
     {
      return $this->belongsToMany(User::class)
                 ->withPivot('paid') 
                 ->withTimestamps();
     }
     public function journeyUsers()
     {
      return $this->hasMany(JourneyUser::class);
     }
  
     public function favorites()
     {
      return $this->morphMany(Favorite::class, 'favoritable');
     }
      
       public function getIsFavoriteAttribute()
       {
       if (!auth()->check()) {
        return false;
       }

      return $this->favorites()->where('user_id', auth()->id())->exists();
     }

         public function locations()
    {
        return $this->morphMany(Location::class, 'locatable');
    }
       public function feedbacks()
    {
    return $this->morphMany(Feedback::class, 'feedbackable');
    }
    public function registrationGroups() {
        return $this->hasMany(JourneyRegistrationGroup::class);
    }
   

}
