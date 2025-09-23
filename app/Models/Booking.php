<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'hotel_id',
        'room_id',
        'unit_id',
        'amount',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'booking_date',
        'start_time',
        'end_time',
        'notes',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    
    public function getAmountAttribute($value)
    {
    return (int) $value;
    }
   
     public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(HotelRoomUnit::class, 'unit_id');
    }
}
