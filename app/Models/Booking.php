<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'booking_date',
        'start_time',
        'end_time',
        'notes',
        'bookable_id',
        'bookable_type'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    
    ];
     public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
     public function hotelDetail()
    {
        return $this->hasOne(HotelBookingDetail::class);
    }

    public function busDetail()
    {
        return $this->hasOne(BusBookingDetail::class);
    }

    public function environmentDetail()
    {
        return $this->hasOne(EnvironmentBookingDetail::class);
    }

    // Get type-specific details dynamically
    public function getDetailAttribute()
    {
        return match($this->bookable_type) {
            Hotel::class => $this->hotelDetail,
            Bus::class => $this->busDetail,
            Environment::class => $this->environmentDetail,
            default => null
        };
    }
}
