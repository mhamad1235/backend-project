<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    public function run()
    {   
        for ($i=0; $i <20 ; $i++) { 
           $restaurant = new Restaurant([
            'account_id'=>1,
            'latitude' => '40.730610',
            'longitude' => '-73.935242',
            'city_id' => 3,
            // add other fillable fields here if needed
        ]);

        // English translations
        $restaurant->translateOrNew('en')->name = 'Seafood Delight';
        $restaurant->translateOrNew('en')->description = 'Best seafood restaurant in town.';

        // Arabic translations
        $restaurant->translateOrNew('ar')->name = 'مأكولات بحرية شهية';
        $restaurant->translateOrNew('ar')->description = 'أفضل مطعم مأكولات بحرية في المدينة.';

        $restaurant->save();

        }
       
        // Add more restaurants as needed
    }
}
