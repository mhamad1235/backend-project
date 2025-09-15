<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JourneyRegistrationGroup extends Model
{
     protected $fillable = [
        'journey_id','contact_user_id','type','adults_count','children_count','paid','status','total_people'
    ];

    public function journey() { 
        return $this->belongsTo(Journey::class); 
    }
    
 
   public function contactUser()
   {
    return $this->belongsTo(User::class, 'contact_user_id');
   } 

}
