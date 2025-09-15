<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'paymentable_id',
        'paymentable_type',
        'user_id',
        'fib_payment_id',
        'amount',
        'status',
        'fib_response',
        'meta'
    ];

    protected $casts = [
        'fib_response' => 'array',
          'meta' => 'array',
    ];

    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


     // use this function in this filed that have a payment
     //     public function payment()
     // {
     //     return $this->morphOne(Payment::class, 'paymentable');
     // }

}
