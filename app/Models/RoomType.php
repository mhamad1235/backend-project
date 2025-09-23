<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class RoomType extends Model
{
    use HasFactory, Translatable, SoftDeletes;

    protected $fillable = [];
    public $translatedAttributes = ['name'];
    protected $hidden = ['translations','created_at', 'updated_at', 'deleted_at'];

    public function rooms()
    {
        return $this->hasMany(HotelRoom::class);
    }

   
}
