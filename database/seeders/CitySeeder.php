<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\ActiveStatus;
class CitySeeder extends Seeder
{
  public function run()
    {
        // Clear existing data
        DB::table('cities')->delete();
        DB::table('city_translations')->delete();

        $cities = [
            [
                "en" => ["name" => "Al-Anbar"],
                "ar" => ["name" => "الأنبار"],
                "ku" => ["name" => "ئەنبار"],
            ],
            [
                "en" => ["name" => "Babil"],
                "ar" => ["name" => "بابل"],
                "ku" => ["name" => "بابل"],
            ],
            [
                "en" => ["name" => "Baghdad"],
                "ar" => ["name" => "بغداد"],
                "ku" => ["name" => "بەغداد"],
            ],
            [
                "en" => ["name" => "Basra"],
                "ar" => ["name" => "البصرة"],
                "ku" => ["name" => "بەسڕە"],
            ],
            [
                "en" => ["name" => "Dhi Qar"],
                "ar" => ["name" => "ذي قار"],
                "ku" => ["name" => "زیقار"],
            ],
            [
                "en" => ["name" => "Al-Qādisiyyah"],
                "ar" => ["name" => "القادسية"],
                "ku" => ["name" => "قادسیە"],
            ],
            [
                "en" => ["name" => "Diyala"],
                "ar" => ["name" => "ديالى"],
                "ku" => ["name" => "دیالە"],
            ],
            [
                "en" => ["name" => "Duhok"],
                "ar" => ["name" => "دهوك"],
                "ku" => ["name" => "دهۆک"],
            ],
            [
                "en" => ["name" => "Erbil"],
                "ar" => ["name" => "أربيل"],
                "ku" => ["name" => "هەولێر"],
            ],
            [
                "en" => ["name" => "Halabja"],
                "ar" => ["name" => "حلبجة"],
                "ku" => ["name" => "هەڵەبجە"],
            ],
            [
                "en" => ["name" => "Karbala"],
                "ar" => ["name" => "كربلاء"],
                "ku" => ["name" => "کەربەلاء"],
            ],
            [
                "en" => ["name" => "Kirkuk"],
                "ar" => ["name" => "كركوك"],
                "ku" => ["name" => "کەرکووک"],
            ],
            [
                "en" => ["name" => "Maysan"],
                "ar" => ["name" => "ميسان"],
                "ku" => ["name" => "میسان"],
            ],
            [
                "en" => ["name" => "Muthanna"],
                "ar" => ["name" => "المثنى"],
                "ku" => ["name" => "موسەنا"],
            ],
            [
                "en" => ["name" => "Najaf"],
                "ar" => ["name" => "النجف"],
                "ku" => ["name" => "نەجەف"],
            ],
            [
                "en" => ["name" => "Ninawa"],
                "ar" => ["name" => "نينوى"],
                "ku" => ["name" => "نەینەوا"],
            ],
            [
                "en" => ["name" => "Salah Al"],
                "ar" => ["name" => "صلاح الدين"],
                "ku" => ["name" => "سەلاحەدین"],
            ],
            [
                "en" => ["name" => "Sulaymaniyah"],
                "ar" => ["name" => "السليمانية"],
                "ku" => ["name" => "سلێمانی"],
            ],
            [
                "en" => ["name" => "Wasit"],
                "ar" => ["name" => "واسط"],
                "ku" => ["name" => "واست"],
            ],
        ];

        foreach ($cities as $cityData) {
            $city = new City(['status' => ActiveStatus::ACTIVE]);

            foreach ($cityData as $locale => $translation) {
                $city->translateOrNew($locale)->name = $translation['name'];
            }

            $city->save();
        }

     
    }
}
