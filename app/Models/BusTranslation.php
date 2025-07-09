<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusTranslation extends Model
{   
    public $table = 'buses_translations';
    public $timestamps = false;
    protected $fillable = ['owner_name'];
}
