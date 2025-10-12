<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Environment;
use App\Models\Property;

class EnvironmentSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['cabin', 'lake'];
        $imagePaths = [
            'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png',
            'uploads/3Qx5AIc78bjCTEnZ71mTYxmGCqUteO80c0JvnC9j.png',
        ];

        // Get all property IDs in advance
        $propertyIds = Property::pluck('id')->all();

        for ($i = 0; $i < 20; $i++) {
            $env = new Environment([
                'phone' => '1234567890',
                'latitude' => '35.6895',
                'longitude' => '139.6917',
                'city_id' => 1,
                'type' => $types[$i % 2],
            ]);

            // Translations
            $env->translateOrNew('en')->name = 'Blue Lake ' . $i;
            $env->translateOrNew('en')->description = 'A beautiful blue lake.';

            $env->translateOrNew('ar')->name = 'البحيرة الزرقاء ' . $i;
            $env->translateOrNew('ar')->description = 'بحيرة زرقاء جميلة.';

            $env->save();

            // Attach random images
            $selectedImages = collect($imagePaths)->random(rand(1, 2))->all();
            foreach ($selectedImages as $path) {
                $env->images()->create(['path' => $path]);
            }

            // Attach 1–3 random properties if available
            if (!empty($propertyIds)) {
                $randomPropertyIds = collect($propertyIds)->random(rand(1, min(3, count($propertyIds))));
                $env->properties()->attach($randomPropertyIds);
            }
        }
    }
}
