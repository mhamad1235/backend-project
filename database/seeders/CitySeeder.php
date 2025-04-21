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
        // Delete all the records from the table
        DB::table('cities')->delete();
        DB::table('city_translations')->delete();

        $cities = [
            [
                "en" => ["name" => "Erbil"],
                "ar" => ["name" => "أربيل"],
                "ku" => ["name" => "هەولێر"],
            ],
            [
                "en" => ["name" => "Duhok"],
                "ar" => ["name" => "دهوك"],
                "ku" => ["name" => "دهۆک"],
            ],
            [
                "en" => ["name" => "Sulaymaniyah"],
                "ar" => ["name" => "السليمانية"],
                "ku" => ["name" => "سلێمانی"],
            ],
            [
                "en" => ["name" => "Kirkuk"],
                "ar" => ["name" => "كركوك"],
                "ku" => ["name" => "کەرکووک"],
            ],
            [
                "en" => ["name" => "Halabja"],
                "ar" => ["name" => "حلبجة"],
                "ku" => ["name" => "هەڵەبجە"],
            ],
        ];

        // $cities = [
        //     ["en" => "Al-Anbar", "ar" => "الأنبار", "ku" => "ئەنبار"],
        //     ["en" => "Babil", "ar" => "بابل", "ku" => "بابل"],
        //     ["en" => "Baghdad", "ar" => "بغداد", "ku" => "بەغداد"],
        //     ["en" => "Basra", "ar" => "البصرة", "ku" => "بصرە"],
        //     ["en" => "Dhi Qar", "ar" => "ذي قار", "ku" => "زیقار"],
        //     ["en" => "Al-Qādisiyyah", "ar" => "القادسية", "ku" => "قادسیە"],
        //     ["en" => "Diyala", "ar" => "ديالى", "ku" => "دیالە"],
        //     ["en" => "Duhok", "ar" => "دهوك", "ku" => "دهۆک"],
        //     ["en" => "Erbil", "ar" => "أربيل", "ku" => "هەولێر"],
        //     ["en" => "Karbala", "ar" => "كربلاء", "ku" => "کەربەلاء"],
        //     ["en" => "Kirkuk", "ar" => "كركوك", "ku" => "کەرکووک"],
        //     ["en" => "Maysan", "ar" => "ميسان", "ku" => "میسان"],
        //     ["en" => "Muthanna", "ar" => "المثنى", "ku" => "موسەنا"],
        //     ["en" => "Najaf", "ar" => "النجف", "ku" => "نەجەف"],
        //     ["en" => "Ninawa", "ar" => "نينوى", "ku" => "نەینەوا"],
        //     ["en" => "Salah Al", "ar" => "صلاح الدين", "ku" => "سەلاحەدین"],
        //     ["en" => "Sulaymaniyah", "ar" => "السليمانية", "ku" => "سلێمانی"],
        //     ["en" => "Wasit", "ar" => "واسط", "ku" => "واست"],
        // ];

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
            $city = City::create(); // only if you have no required non-translatable columns
    
            foreach ($cityData as $locale => $translation) {
                $city->translateOrNew($locale)->name = $translation['name'];
            }
    
            $city->save();
        }
        $city = City::create([
            'status' => ActiveStatus::ACTIVE
        ]);
    }
}
