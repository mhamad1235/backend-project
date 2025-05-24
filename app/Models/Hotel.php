<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = ['account_id', 'name', 'address'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

}
