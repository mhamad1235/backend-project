<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeminiTable extends Model
{  protected $table = 'geminitable'; 
        protected $fillable = ['data'];
        protected $casts = [
        'data' => 'array'];
}
