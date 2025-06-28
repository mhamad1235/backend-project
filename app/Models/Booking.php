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
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
     public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
