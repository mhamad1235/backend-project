<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\RoomType;
use App\Models\RoomAvailability;
use App\Models\HotelRoomUnit;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Room Types with translations
        $roomTypesData = [
            [
                'en' => ['name' => 'Single Room'],
                'ar' => ['name' => 'غرفة مفردة'],
                'ku' => ['name' => 'ژووری تاک'],
            ],
            [
                'en' => ['name' => 'Double Room'],
                'ar' => ['name' => 'غرفة مزدوجة'],
                'ku' => ['name' => 'ژووری دوو'],
            ],
            [
                'en' => ['name' => 'Suite'],
                'ar' => ['name' => 'جناح'],
                'ku' => ['name' => 'سویت'],
            ],
        ];

        $roomTypes = [];
        foreach ($roomTypesData as $typeData) {
            $roomType = new RoomType();
            $roomType->save();
            foreach ($typeData as $locale => $data) {
                $roomType->translateOrNew($locale)->name = $data['name'];
            }
            $roomType->save();
            $roomTypes[] = $roomType;
        }

        // 2. Create Hotels with translations
        for ($i = 1; $i < 2; $i++) {
            $hotel = new Hotel([
                'phone'      => '+96470000000' . $i,
                'latitude'   => '36.19' . $i,
                'longitude'  => '44.00' . $i,
                'city_id'    => 1,
                'account_id' => 1,
            ]);

            $hotel->translateOrNew('en')->name = "Erbil Palace Hotel $i";
            $hotel->translateOrNew('en')->description = "Luxury hotel in Erbil number $i";

            $hotel->translateOrNew('ar')->name = "فندق قصر أربيل $i";
            $hotel->translateOrNew('ar')->description = "فندق فاخر في أربيل رقم $i";

            $hotel->translateOrNew('ku')->name = "هوتێلی قەصر هەولێر $i";
            $hotel->translateOrNew('ku')->description = "هوتێلێکی دەلووکس لە هەولێر ژمارە $i";

            $hotel->save();

            // 3. Create Hotel Rooms for each room type
            foreach ($roomTypes as $roomType) {
                $quantity = match ($roomType->translate('en')->name) {
                    'Single Room' => 4,
                    'Double Room' => 6,
                    'Suite'       => 2,
                    default       => 1,
                };

                $price = match ($roomType->translate('en')->name) {
                    'Single Room' => 50.0,
                    'Double Room' => 90.0,
                    'Suite'       => 200.0,
                    default       => 100.0,
                };

                $hotelRoom = HotelRoom::create([
                    'hotel_id'     => $hotel->id,
                    'room_type_id' => $roomType->id,
                    'name'         => $roomType->translate('en')->name . " - Room $i",
                    'guest'        => $roomType->translate('en')->name === 'Suite'
                        ? 4
                        : ($roomType->translate('en')->name === 'Double Room' ? 2 : 1),
                    'bedroom'      => $roomType->translate('en')->name === 'Suite'
                        ? 2
                        : ($roomType->translate('en')->name === 'Double Room' ? 1 : 1),
                    'beds'         => $roomType->translate('en')->name === 'Suite'
                        ? 2
                        : ($roomType->translate('en')->name === 'Double Room' ? 1 : 1),
                    'bath'         => $roomType->translate('en')->name === 'Suite'
                        ? 2
                        : ($roomType->translate('en')->name === 'Double Room' ? 1 : 1),
                    'quantity'     => $quantity,
                    'price'        => $price,
                ]);

                // 4. Generate units for this room
                $units = [];
                for ($j = 1; $j <= $quantity; $j++) {
                    $units[] = HotelRoomUnit::create([
                        'hotel_room_id' => $hotelRoom->id,
                        'room_number'   => $j,
                        'is_available'  => true,
                    ]);
                }

                // 5. Generate availability for each unit for next 7 days
                foreach ($units as $unit) {
                    foreach (range(0, 6) as $day) {
                        RoomAvailability::create([
                            'hotel_room_unit_id' => $unit->id,
                            'date'               => now()->addDays($day)->toDateString(),
                   
                        ]);
                    }
                }
            }
        }
    }
}
