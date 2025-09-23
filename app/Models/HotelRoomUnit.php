<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelRoomUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_room_id',
        'room_number',
        'is_available',
    ];

    protected $hidden = ['created_at', 'updated_at'];
    // Relationship to the parent room
    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'hotel_room_id');
    }
    public function availabilities()
    {
    return $this->hasMany(RoomAvailability::class, 'hotel_room_unit_id');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'hotel_room_unit_id');
    }
}
