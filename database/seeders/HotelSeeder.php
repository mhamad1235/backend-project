<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hotel;
class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { for ($i=0; $i <20 ; $i++) { 
            $hotel = new Hotel([
            'phone' => '5551234567',
            'latitude' => '40.7128',
            'longitude' => '-74.0060',
            'city_id' => 3,
        ]);

        $hotel->translateOrNew('en')->name = 'Grand Plaza';
        $hotel->translateOrNew('en')->description = 'Luxury hotel in downtown.';

        $hotel->translateOrNew('ar')->name = 'جراند بلازا';
        $hotel->translateOrNew('ar')->description = 'فندق فاخر في وسط المدينة.';

        $hotel->save();
    }
          
    }
}
