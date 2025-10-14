<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\RoomAvailability;
use App\Models\HotelRoomUnit;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define static room types and translations (no RoomType model)
        $roomTypes = [
            [
                'key' => 'single',
                'en' => 'Single Room',
                'ar' => 'غرفة مفردة',
                'ku' => 'ژووری تاک',
                'quantity' => 4,
                'price' => 50.0,
                'guest' => 1,
                'bedroom' => 1,
                'beds' => 1,
                'bath' => 1,
            ],
            [
                'key' => 'double',
                'en' => 'Double Room',
                'ar' => 'غرفة مزدوجة',
                'ku' => 'ژووری دوو',
                'quantity' => 6,
                'price' => 90.0,
                'guest' => 2,
                'bedroom' => 1,
                'beds' => 1,
                'bath' => 1,
            ],
            [
                'key' => 'suite',
                'en' => 'Suite',
                'ar' => 'جناح',
                'ku' => 'سویت',
                'quantity' => 2,
                'price' => 200.0,
                'guest' => 4,
                'bedroom' => 2,
                'beds' => 2,
                'bath' => 2,
            ],
        ];

        // 2. Create Hotels
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

            // 3. Create Rooms (no RoomType model)
            foreach ($roomTypes as $roomType) {
                $hotelRoom = new HotelRoom([
                    'hotel_id' => $hotel->id,
                    'guest'    => $roomType['guest'],
                    'bedroom'  => $roomType['bedroom'],
                    'beds'     => $roomType['beds'],
                    'bath'     => $roomType['bath'],
                    'quantity' => $roomType['quantity'],
                    'price'    => $roomType['price'],
                ]);

                $hotelRoom->translateOrNew('en')->name = $roomType['en'] . " - Room $i";
                $hotelRoom->translateOrNew('ar')->name = $roomType['ar'] . " - غرفة $i";
                $hotelRoom->translateOrNew('ku')->name = $roomType['ku'] . " - ژوور $i";

                $hotelRoom->save();

                // 4. Create Units
                $units = [];
                for ($j = 1; $j <= $roomType['quantity']; $j++) {
                    $units[] = HotelRoomUnit::create([
                        'hotel_room_id' => $hotelRoom->id,
                        'room_number'   => $j,
                        'is_available'  => true,
                    ]);
                }

                // 5. Create Availability
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
