<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;
class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $places = [
            [
                'image' => 'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png',
                'latitude' => 48.8584,
                'longitude' => 2.2945,
                'city_id' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Eiffel Tower',
                        'description' => 'A famous monument in Paris.',
                    ],
                    'ar' => [
                        'name' => 'Tour Eiffel',
                        'description' => 'Un monument célèbre à Paris.',
                    ],
                     'ku' => [
                        'name' => 'برج القاهرة',
                        'description' => 'برج طويل في مدينة القاهرة بمصر.',
                    ],
                ],
            ],
            [
                'image' => 'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png',
                'latitude' => 30.0459,
                'longitude' => 31.2243,
                'city_id' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Cairo Tower',
                        'description' => 'A tall free-standing tower in Cairo, Egypt.',
                    ],
                    'ar' => [
                        'name' => 'برج القاهرة',
                        'description' => 'برج طويل في مدينة القاهرة بمصر.',
                    ],
                      'ku' => [
                        'name' => 'برج القاهرة',
                        'description' => 'برج طويل في مدينة القاهرة بمصر.',
                    ],
                ],
            ],
        ];

        foreach ($places as $placeData) {
            // Create place without image
            $place = Place::create([
                'latitude' => $placeData['latitude'],
                'longitude' => $placeData['longitude'],
                'city_id'   =>$placeData['city_id']
            ]);

            // Set translations
            foreach ($placeData['translations'] as $locale => $translation) {
                $place->translateOrNew($locale)->name = $translation['name'];
                $place->translateOrNew($locale)->description = $translation['description'];
            }

            $place->save();

            // Attach image via relation
            $place->images()->create([
                'path' => $placeData['image'],
            ]);
        }
    }

}
