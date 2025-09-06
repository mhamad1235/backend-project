<?php

namespace App\Models;
use App\Enums\ActiveStatus;
use App\Traits\ActiveScopTrait;
use App\Traits\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

    class City extends Model implements TranslatableContract
    {
        use SoftDeletes, Translatable;

        protected $guarded = [];
        public $translatedAttributes = ['name'];
        protected $hidden = ["translations","created_at", "updated_at"];
        protected $cascadeDeletes = ['translations'];

        protected $casts = [
            'status' => ActiveStatus::class,
            'is_delivery' => 'boolean',
        ];

        public function buses()
        {   
        return $this->hasMany(Bus::class);
        }

         public function cabins()
        {   
        return $this->hasMany(Cabin::class);
        }

        public function restaurants()
        {
        return $this->hasMany(Restaurant::class);
        }


}

