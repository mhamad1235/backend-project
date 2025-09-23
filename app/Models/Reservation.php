<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
     protected $fillable = [
        'hotel_room_unit_id',
        'guest_name',
        'check_in',
        'check_out',
        'total_price',
    ];
        protected $hidden = ["created_at", "updated_at"];
   
     public function unit()
    {
        return $this->belongsTo(HotelRoomUnit::class, 'hotel_room_unit_id');
    }
}
