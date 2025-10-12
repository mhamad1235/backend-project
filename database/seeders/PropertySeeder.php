<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;
class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $properties = [
            ['name' => 'WiFi', 'image_path' => '/assets/images/auth-one-bg.jpg'],
            ['name' => 'Parking', 'image_path' => '/assets/images/auth-one-bg.jpg'],
            ['name' => 'Swimming Pool', 'image_path' => '/assets/images/auth-one-bg.jpg'],
            ['name' => 'Air Conditioning', 'image_path' => '/assets/images/auth-one-bg.jpg'],
        ];

        foreach ($properties as $property) {
            Property::create($property);
        }
    }
}
