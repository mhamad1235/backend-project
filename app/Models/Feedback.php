<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{


    public $table="feedbacks";
    protected $hidden = ['created_at', 'updated_at'];
     protected $fillable = [
        'rating',
        'comment',
        'status',
        'user_id',
    ];
     public function feedbackable()
    {
        return $this->morphTo();
    }

    public function user()
    {
    return $this->belongsTo(User::class);
    }

    public function scopeVisible($query)
    {
    return $query->where('status', 'visible');
    }

}
