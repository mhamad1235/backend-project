<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JourneyTranslation extends Model
{      
       public $table = 'journeys_translations_user'; // Specify the table name if different
       public $timestamps = false; 
       protected $fillable = ['locale', 'name', 'description']; // for JourneyTranslation

}
