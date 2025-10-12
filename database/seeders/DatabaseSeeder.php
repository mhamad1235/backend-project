<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call(CitySeeder::class);
        $this->call(PropertySeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(PlaceSeeder::class);
        $this->call(HotelSeeder::class);
        $this->call(EnvironmentSeeder::class);
        $this->call(JourneySeeder::class);
        $this->call(PlaceSeeder::class);
        $this->call(FoodCategorySeeder::class);
        $this->call(RestaurantSeeder::class);
    }
}
