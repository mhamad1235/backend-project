<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = ['path'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $visible=['path'];
      public function imageable()
    {
        return $this->morphTo();
    }
     public function getPathAttribute()
    {
        return Storage::disk('s3')->url($this->attributes['path']);
    }


}
