<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelBookingDetail extends Model
{

    public $table='hotel_booking_details';
     protected $fillable = [
        'rooms',
        'adults',
        'children'
    ];
    public $timestamps=false;
     public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
