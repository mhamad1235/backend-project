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
use App\Models\Reservation;
use App\Models\Booking;
use Carbon\Carbon;

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
     public function createReservation(Request $request)
    {
        $validated = $request->validate([
            'hotel_room_unit_id' => 'required|exists:hotel_room_units,id',
            'guest_name'         => 'required|string|max:255',
            'check_in'           => 'required|date|after_or_equal:today',
            'check_out'          => 'required|date|after:check_in',
        ]);

        try {
            $account = auth('account')->user();
            $hotelId = $account->hotel->id;
             $start   = Carbon::parse($validated['check_in']);
            $endIncl = Carbon::parse($validated['check_out'])->subDay();
            $nights  = $start->diffInDays($endIncl) + 1;


            $unit = HotelRoomUnit::with('room')->findOrFail($validated['hotel_room_unit_id']);

            if ($unit->room->hotel_id !== $hotelId) {
                return $this->jsonResponse(false, 'You are not authorized to update this unit ❌', 403);
            }

         
          
                 Reservation::updateOrCreate(
                    [
                        'hotel_room_unit_id' => $unit->id,
                        'guest_name'         => $validated['guest_name'],
                        'check_in'           => $validated['check_in'],
                        'check_out'          => $validated['check_out'],
                    ],
                    [
                        'total_price' => $unit->room->price,
                       
                    ]
                );
                  $rows = [];
                for ($d = $start->copy(); $d->lte($endIncl); $d->addDay()) {
                    $rows[] = [
                        'hotel_room_unit_id' => $unit->id,
                        'date'               => $d->toDateString(),
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }
          if (!empty($rows)) {
    RoomAvailability::upsert(
        $rows,
        ['hotel_room_unit_id', 'date'],
        ['updated_at']
    );
}

            

            return $this->jsonResponse(true, 'Room availability records created/updated successfully ✅', 200);
        } catch (\Throwable $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
    } 

   public function getReservation(Request $request)
{
   
    try {
        $account   = auth('account')->user();
        $hotelId   = $account->hotel->id;

        $date            = $request->input('date');             
        $guestName       = trim((string) $request->input('guest_name'));
        $roomNumber      = trim((string) $request->input('room_number'));
        $arriveToday     = $request->boolean('arrive_today');
        $departingToday  = $request->boolean('departing_today');
        $today           = Carbon::today()->toDateString();

        
        $bookings = Booking::query()
            ->where('hotel_id', $hotelId)
            ->with([
                'user:id,name',
                'unit'      
            ])
            ->when($guestName !== '', fn ($q) =>
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$guestName}%")
                )
            )
            ->when($roomNumber !== '', fn ($q) =>
                $q->whereHas('unit', fn ($u) =>
                    $u->where('room_number', 'like', "%{$roomNumber}%") 
                )
            )
            ->when($date, fn ($q) => $q->whereDate('start_time', $date))
            ->when($arriveToday, fn ($q) => $q->whereDate('start_time', $today))
            ->when($departingToday, fn ($q) => $q->whereDate('end_time', $today))
            ->get();

 
        $reservations = Reservation::query()
            ->whereHas('unit.room', function ($q) use ($hotelId, $roomNumber) {
                $q->where('hotel_id', $hotelId);
                if ($roomNumber !== '') {
                    $q->where('room_number', 'like', "%{$roomNumber}%"); 
                }
            })
            ->when($guestName !== '', fn ($q) =>
                $q->where('guest_name', 'like', "%{$guestName}%")
            )
            ->when($date, fn ($q) => $q->whereDate('check_in', $date))
            ->when($arriveToday, fn ($q) => $q->whereDate('check_in', $today))
            ->when($departingToday, fn ($q) => $q->whereDate('check_out', $today))
            ->with(['unit.room'])
            ->get()
            ->map(function ($r) {
                $checkIn  = Carbon::parse($r->check_in);
                $checkOut = Carbon::parse($r->check_out);
                $r->nights = $checkIn->diffInDays($checkOut);
                return $r;
            });
        $items = collect();

        // Map bookings
        foreach ($bookings as $b) {
            $start = $b->start_time ? Carbon::parse($b->start_time) : null;
            $end   = $b->end_time ? Carbon::parse($b->end_time) : null;

            $items->push([
                'type'            => 'booking',
                'id'              => $b->id,
                'guest_name'      => optional($b->user)->name, 
                'room_number'     => (int) $b->unit->room_number ?? null,
                'start_date'      => $start?->toDateString(),
                'end_date'        => $end?->toDateString(),
                'nights'          => ($start && $end) ? $start->diffInDays($end) : null,
                'price'           => $b->amount * (($start && $end) ? $start->diffInDays($end) : 1),
             
            ]);
        }
       
        foreach ($reservations as $r) {
            $items->push([
                'type'            => 'reservation',
                'id'              => $r->id,
                'guest_name'      => $r->guest_name,
                'room_number'     => (int) $r->unit->room_number ?? null,
                'start_date'      => Carbon::parse($r->check_in)->toDateString(),
                'end_date'        => Carbon::parse($r->check_out)->toDateString(),
                'nights'          => $r->nights,
                'price'           => $r->total_price * $r->nights,
       
            ]);
        }
        $items = $items
            ->sortBy(fn ($row) => $row['start_date'] ?? '9999-12-31')
            ->values();

        return $this->jsonResponse(true, 'Reservations & bookings fetched successfully ✅', 200, [
            'count'     => $items->count(),
            'items'     => $items,
            'debug'     => [
                'filters' => compact('date', 'guestName', 'roomNumber', 'arriveToday', 'departingToday'),
            ],
        ]);

    } catch (\Throwable $e) {
        return $this->jsonResponse(false, $e->getMessage(), 500);
    }
}

   public function getHotelUnit(Request $request)
   {
    try {
        $account = auth('account')->user();
        $hotelId = $account->hotel->id;
        $today = Carbon::now()->toDateString();
        $base = HotelRoomUnit::query()
            ->whereHas('room', fn($q) => $q->where('hotel_id', $hotelId));

        $availableCount = (clone $base)
            ->whereDoesntHave('availabilities', fn($q) => $q->whereDate('date', $today))
            ->count();

        $unavailableCount = (clone $base)
            ->whereHas('availabilities', fn($q) => $q->whereDate('date', $today))
            ->count();

        $status = $request->input('status');
        $unitsQuery = clone $base;

        if ($status === 'available') {
            $unitsQuery->whereDoesntHave('availabilities', fn($q) => $q->whereDate('date', $today));
        } elseif ($status === 'unavailable') {
            $unitsQuery->whereHas('availabilities', fn($q) => $q->whereDate('date', $today));
        } else {
            $unitsQuery->withExists([
                'availabilities as has_availability_today' => fn($q) => $q->whereDate('date', $today),
            ]);
        }

        $units = $unitsQuery->paginate(40);

        if (!$status) {
            $units->getCollection()->transform(function ($u) {
                $u->available_today = !$u->has_availability_today;
                unset($u->has_availability_today);
                return $u;
            });
        }
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Hotel units fetched successfully ✅',
            'data' => $units->items(), 
            'summary' => [
                'available' => $availableCount,
                'unavailable' => $unavailableCount,
                'total' => $availableCount + $unavailableCount,
            ],
            'pagination' => [
                'current_page' => $units->currentPage(),
                'last_page' => $units->lastPage(),
                'per_page' => $units->perPage(),
                'total' => $units->total(),
            ]
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'result' => false,
            'status' => 500,
            'message' => $e->getMessage(),
        ]);
    }
}
 
   
   public function todayActivity()
   {
    try {
        $account = auth('account')->user();
        $hotelId = $account->hotel->id;
        $today   = Carbon::now()->toDateString();

        $totalUnits = HotelRoomUnit::whereHas('room', fn ($q) => $q->where('hotel_id', $hotelId))->count();

        $check_in_today = HotelRoomUnit::whereHas('room', fn ($q) => $q->where('hotel_id', $hotelId))
            ->whereHas('reservations', fn ($q) => $q->whereDate('check_in', $today))
            ->count();
            $check_in_today_online = Booking::whereDate('start_time', $today)->count();
            $check_out_today_online = Booking::whereDate('end_time', $today)->count();
        $check_out_today = HotelRoomUnit::whereHas('room', fn ($q) => $q->where('hotel_id', $hotelId))
            ->whereHas('reservations', fn ($q) => $q->whereDate('check_out', $today))
            ->count();

        $data = [
           'check_in_today' => $check_in_today,
           'check_out_today'=>$check_out_today,
           'check_in_today_online' =>$check_in_today_online,
           'check_out_today_online'=>$check_out_today_online
        ];

        return $this->jsonResponse(true, 'Today\'s activity fetched successfully ✅', 200, $data);
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
