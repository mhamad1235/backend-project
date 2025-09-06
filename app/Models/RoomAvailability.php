<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomAvailability extends Model
{
     use HasFactory;

    protected $fillable = [
        'hotel_room_unit_id',
        'date',
        'available',
    ];
        protected $hidden = ["created_at", "updated_at"];
     protected $casts = [
        'available' => 'boolean',
     
    ];
     public function unit()
    {
        return $this->belongsTo(HotelRoomUnit::class, 'hotel_room_unit_id');
    }
}
