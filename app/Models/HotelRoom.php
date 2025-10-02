<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'name',
        'guest',
        'bedroom',
        'beds',
        'bath',
        'quantity',
        'price',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function getPriceAttribute($value)
    {
    return (int) $value;
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function type()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class);
    }
     public function units()
    {
        return $this->hasMany(HotelRoomUnit::class);
    }
    
    public function properties()
    {
        return $this->morphToMany(Property::class, 'propertyable');
    }


    protected static function booted()
    {
        static::created(function ($room) {
            for ($i = 1; $i <= $room->quantity; $i++) {
                $room->units()->create([
                    'room_number' => $i,
                    'room_type' => $room->type ? $room->type->name : null,
                ]);
            }
        });
    }
}
