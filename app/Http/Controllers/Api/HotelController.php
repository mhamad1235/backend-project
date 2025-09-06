<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelRoomUnit;
use App\Models\RoomAvailability;
use App\Models\RoomType;
class HotelController extends Controller
{
    public function __construct()
    {
        App::setLocale(app()->getLocale());
    }

    public function getHotel()
    {
        try {
            $account = auth('account')->user();
            $hotel = $account->hotel()->with([
                'city', 'images', 'feedbacks', 'properties'
            ])->first();

            return $this->jsonResponse(true, 'Hotel fetched successfully ✅', 200, $hotel);
        } catch (\Throwable $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
    }
    public function createTypeRoom(Request $request){
 
       $validated = $request->validate([
        'en' => 'required|string|max:255',
        'ar' => 'required|string|max:255',
        'ku' => 'required|string|max:255',
    ]);

    try {
        $roomType = new RoomType();

        foreach (['en', 'ar', 'ku'] as $locale) {
            $roomType->translateOrNew($locale)->name = $validated[$locale];
        }

        $roomType->save();

        return response()->json([
            'success' => true,
            'message' => 'Room type created successfully ✅',
            'data' => $roomType
        ], 201);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
    }
    public function createHotelRoom(Request $request)
    {

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'name'         => 'required|string|max:255',
            'guest'        => 'required|integer|min:1',
            'bedroom'      => 'required|integer|min:0',
            'beds'         => 'required|integer|min:0',
            'bath'         => 'required|integer|min:0',
            'quantity'     => 'required|integer|min:1',
            'price'        => 'required|numeric|min:0',
        ]);

        try {
            $account = auth('account')->user();
            $hotelId = $account->hotel->id;

            $room = HotelRoom::create([
                'hotel_id' => $hotelId,
            ] + $validated);

            return $this->jsonResponse(true, 'Hotel room created successfully ✅', 201, $room);
        } catch (\Throwable $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
    }

    public function createUnitRoom(Request $request)
    {
        $validated = $request->validate([
            'hotel_room_id' => 'required|exists:hotel_rooms,id',
            'room_number'   => 'required|string|max:50',
            'is_available'  => 'boolean',
        ]);

        try {
            $account = auth('account')->user();
            $hotelId = $account->hotel->id;

            $hotelRoom = HotelRoom::findOrFail($validated['hotel_room_id']);

            if ($hotelRoom->hotel_id !== $hotelId) {
                return $this->jsonResponse(false, 'You are not authorized to create a unit for this room ❌', 403);
            }

            $unit = HotelRoomUnit::create([
                'hotel_room_id' => $hotelRoom->id,
                'room_number'   => $validated['room_number'],
                'is_available'  => $validated['is_available'] ?? true,
            ]);

            return $this->jsonResponse(true, 'Room unit created successfully ✅', 201, $unit);
        } catch (\Throwable $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
    }

    public function getHotelRoom()
    {
        try {
            $account = auth('account')->user();
            $hotelId = $account->hotel->id;

            $hotel = Hotel::with([
                'city', 'images', 'rooms.type', 'rooms.units',
            ])->findOrFail($hotelId);

            return $this->jsonResponse(true, 'Hotel rooms fetched successfully ✅', 200, $hotel);
        } catch (\Throwable $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
    }

    public function unitUnavailable(Request $request)
    {
        $validated = $request->validate([
            'hotel_room_unit_id' => 'required|exists:hotel_room_units,id',
            'dates'              => 'required|array|min:1',
            'dates.*'            => 'required|date|after_or_equal:today',
            'available'          => 'required|boolean', 
        ]);

        try {
            $account = auth('account')->user();
            $hotelId = $account->hotel->id;

            $unit = HotelRoomUnit::with('room')->findOrFail($validated['hotel_room_unit_id']);

            if ($unit->room->hotel_id !== $hotelId) {
                return $this->jsonResponse(false, 'You are not authorized to update this unit ❌', 403);
            }

            $availabilities = [];
            foreach ($validated['dates'] as $date) {
                $availabilities[] = RoomAvailability::updateOrCreate(
                    [
                        'hotel_room_unit_id' => $unit->id,
                        'date'               => $date,
                    ],
                    [
                        'available' => $validated['available'],
                       
                    ]
                );
            }

            return $this->jsonResponse(true, 'Room availability records created/updated successfully ✅', 200, $availabilities);
        } catch (\Throwable $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
    } 

   public function getUnitUnavailable($id)
   {
    try {
        $account = auth('account')->user();
        $hotelId = $account->hotel->id;

        $unit = HotelRoomUnit::with('availabilities')->findOrFail($id);
        if ($unit->room->hotel_id !== $hotelId) {
            return $this->jsonResponse(false, 'You are not authorized to view this unit ❌', 403);
        }

        return $this->jsonResponse(true, 'Unit availabilities fetched successfully ✅', 200, $unit->availabilities);
    } catch (\Throwable $e) {
        return $this->jsonResponse(false, $e->getMessage(), 500);
    }
    }  

}
