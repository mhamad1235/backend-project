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
     public function unit()
    {
        return $this->belongsTo(HotelRoomUnit::class, 'hotel_room_unit_id');
    }
}
