<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnavailableSlot extends Model
{
     protected $fillable = [
        'bookable_id',
        'bookable_type',
        'unavailable_date',
        'start_time',
        'end_time',
    ];

    public function bookable()
    {
        return $this->morphTo();
    }
}
