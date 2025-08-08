<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JourneyUser extends Model
{
    protected $table = 'journey_user';
    protected $fillable = ['journey_id', 'user_id', 'paid'];
    public function journey()
     {
      return $this->belongsTo(Journey::class);
      }

      public function user()
      {
      return $this->belongsTo(User::class);
      }

}
