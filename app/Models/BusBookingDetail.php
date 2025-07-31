<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusBookingDetail extends Model
{
    public $table='bus_booking_details';
     public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
