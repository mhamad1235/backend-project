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
                'image' => 'uploads/eiffel.jpg',
                'latitude' => 48.8584,
                'longitude' => 2.2945,
                'translations' => [
                    'en' => [
                        'name' => 'Eiffel Tower',
                        'description' => 'A famous monument in Paris.',
                    ],
                    'fr' => [
                        'name' => 'Tour Eiffel',
                        'description' => 'Un monument célèbre à Paris.',
                    ],
                ],
            ],
            [
                'image' => 'uploads/cairo.jpg',
                'latitude' => 30.0459,
                'longitude' => 31.2243,
                'translations' => [
                    'en' => [
                        'name' => 'Cairo Tower',
                        'description' => 'A tall free-standing tower in Cairo, Egypt.',
                    ],
                    'ar' => [
                        'name' => 'برج القاهرة',
                        'description' => 'برج طويل في مدينة القاهرة بمصر.',
                    ],
                ],
            ],
        ];

        foreach ($places as $placeData) {
            $place = Place::create([
                'image' => $placeData['image'],
                'latitude' => $placeData['latitude'],
                'longitude' => $placeData['longitude'],
            ]);

            foreach ($placeData['translations'] as $locale => $translation) {
                $place->translateOrNew($locale)->name = $translation['name'];
                $place->translateOrNew($locale)->description = $translation['description'];
            }

            $place->save();
        }
    }

}
