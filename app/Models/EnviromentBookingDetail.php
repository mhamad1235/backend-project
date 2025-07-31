<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnviromentBookingDetail extends Model
{
     public $table='environment_booking_details';
     public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
