<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class FoodCategory extends Model implements TranslatableContract
{
    use SoftDeletes, Translatable;

    protected $guarded = [];

    public $translatedAttributes = ['name'];

    protected $hidden = ["translations", "created_at", "updated_at", "deleted_at"];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function foods()
    {
        return $this->hasMany(Food::class, 'category_id');
    }
}
