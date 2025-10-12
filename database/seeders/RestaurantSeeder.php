<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Property;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $imagePaths = [
            'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png',
            'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png',
        ];

        $foodItems = [
            [
                'name' => 'Grilled Salmon',
                'description' => 'Freshly grilled salmon with herbs.',
                'price' => 25,
            ],
            [
                'name' => 'Shrimp Pasta',
                'description' => 'Creamy pasta with garlic shrimp.',
                'price' => 18,
            ],
            [
                'name' => 'Fish Tacos',
                'description' => 'Crispy fish tacos with spicy mayo.',
                'price' => 15,
            ],
        ];

        // Get all property IDs in advance
        $propertyIds = Property::pluck('id')->all();

        for ($i = 0; $i < 2; $i++) {
            // Create restaurant
            $restaurant = new Restaurant([
                'account_id'=> $i + 1,
                'latitude' => '40.730610',
                'longitude' => '-73.935242',
                'city_id' => 3,
            ]);

            // Translations
            $restaurant->translateOrNew('en')->name = 'Seafood Delight ' . ($i + 1);
            $restaurant->translateOrNew('en')->description = 'Best seafood restaurant in town.';

            $restaurant->translateOrNew('ar')->name = 'مأكولات بحرية شهية ' . ($i + 1);
            $restaurant->translateOrNew('ar')->description = 'أفضل مطعم مأكولات بحرية في المدينة.';

            $restaurant->save();

            // Attach images to restaurant
            $selectedImages = collect($imagePaths)->random(rand(1, 2));
            foreach ($selectedImages as $path) {
                $restaurant->images()->create([
                    'path' => $path,
                ]);
            }

            // Attach random properties
            if (!empty($propertyIds)) {
                $randomPropertyIds = collect($propertyIds)->random(rand(1, min(3, count($propertyIds))));
                $restaurant->properties()->attach($randomPropertyIds);
            }

            // Create foods
            $foods = collect($foodItems)->shuffle()->take(rand(2, 3));
            foreach ($foods as $foodData) {
                $food = $restaurant->foods()->create([
                    'name' => $foodData['name'],
                    'description' => $foodData['description'],
                    'price' => $foodData['price'],
                    'is_available' => true,
                    'category_id' => 1, // Ensure this category exists
                ]);

                // Attach 1 image to food
                $food->images()->create([
                    'path' => collect($imagePaths)->random(),
                ]);
            }
        }
    }
}
