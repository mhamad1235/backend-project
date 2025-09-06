<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FoodCategory;
class FoodCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
              $categories = [
            'popular' => ['en' => 'Popular', 'ar' => 'شائع', 'ku' => 'بەناوبانگ'],
            'starters' => ['en' => 'Starters', 'ar' => 'مقبلات', 'ku' => 'دەستپێک'],
            'mains' => ['en' => 'Mains', 'ar' => 'الأطباق الرئيسية', 'ku' => 'خواردنی سەرەکی'],
            'drinks' => ['en' => 'Drinks', 'ar' => 'مشروبات', 'ku' => 'خواردنەوە'],
            'desserts' => ['en' => 'Desserts', 'ar' => 'حلويات', 'ku' => 'شیرینی'],
        ];

        foreach ($categories as $key => $translations) {
            $category = FoodCategory::create(['status' => true]);

            foreach ($translations as $locale => $name) {
                $category->translateOrNew($locale)->name = $name;
            }

            $category->save();
        }
    
    }
}
