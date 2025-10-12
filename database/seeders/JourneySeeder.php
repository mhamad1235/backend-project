<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Journey;
use App\Models\Image;
use App\Models\Location;
class JourneySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run()
    {
        $journeys = [
            [
                'tourist_id' => 1,
                'duration' => 5,
                'price' => 1200,
                'destination' => 'France',
                'start_time'=>'2025-11-01',
                'end_time' =>'2025-11-05',
                'translations' => [
                    'en' => [
                        'name' => 'Romantic Paris Tour',
                        'description' => 'Explore the beautiful city of Paris with romantic walks and fine dining.'
                    ],
                    'ku' => [
                        'name' => 'Visite romantique de Paris',
                        'description' => 'Découvrez la belle ville de Paris avec des promenades romantiques et une cuisine raffinée.'
                    ]
                ],
                'images' => [
                    'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png',
                    'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png'
                ],
                'locations' => [
                    [
                        'latitude' => 48.8566,
                        'longitude' => 2.3522
                    ],
                    [
                        'latitude' => 48.8606,
                        'longitude' => 2.3376
                    ]
                ]
            ],
            [
                'tourist_id' => 2,
                'duration' => 5,
                'price' => 750,
                'destination' => 'Egypt',
                'start_time'=>'2025-11-01',
                'end_time' =>'2025-11-05',
                'translations' => [
                    'en' => [
                        'name' => 'Cairo Adventure',
                        'description' => 'Discover the ancient pyramids and vibrant markets of Cairo.'
                    ],
                    'ku' => [
                        'name' => 'مغامرة القاهرة',
                        'description' => 'اكتشف الأهرامات القديمة والأسواق النابضة بالحياة في القاهرة.'
                    ]
                ],
                'images' => [
                    'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png'
                ],
                'locations' => [
                    [
                        'latitude' => 30.0333,
                        'longitude' => 31.2333
                    ]
                ]
            ]
        ];

        foreach ($journeys as $data) {
            $translations = $data['translations'];
            $images = $data['images'] ?? [];
            $locations = $data['locations'] ?? [];

            unset($data['translations'], $data['images'], $data['locations']);

            // Create journey
            $journey = Journey::create($data);

            // Add translations
            foreach ($translations as $locale => $fields) {
                $journey->translateOrNew($locale)->fill($fields);
            }
            $journey->save();

            // Add images
            foreach ($images as $path) {
                $journey->images()->create([
                    'path' => $path
                ]);
            }

            // Add locations
            foreach ($locations as $loc) {
                $journey->locations()->create($loc);
            }
        }
    }
}
