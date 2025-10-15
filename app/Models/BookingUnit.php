<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingUnit extends Model
{
    protected $table = 'booking_unit'; 
    protected $fillable = [
        'booking_id',
        'unit_id'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
