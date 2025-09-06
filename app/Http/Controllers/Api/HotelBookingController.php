<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FIBPaymentService;
use Illuminate\Support\Facades\Http;
use App\Models\HotelRoomUnit;
use App\Models\RoomAvailability;
use App\Models\HotelRoom;
use App\Models\HotelPayment; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
class HotelBookingController extends Controller
{



    public static function checkAvailability($hotel_id, $room_id, Request $request)
{
    $validated = $request->validate([
        'check_in'   => 'required|date|after_or_equal:today',
        'check_out'  => 'required|date|after:check_in',
        'guests'     => 'required|integer|min:1',
        'rooms'      => 'required|integer|min:1',
    ]);

    $checkIn  = $validated['check_in'];
    $checkOut = $validated['check_out'];
    $roomsRequested = $validated['rooms'];

    $numberOfNights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

    $hotelRoom = HotelRoom::findOrFail($room_id);
    $pricePerNight = $hotelRoom->price;

    // ✅ Get available units
    $availableUnits = HotelRoomUnit::where('hotel_room_id', $room_id)
        ->whereDoesntHave('availabilities', function ($query) use ($checkIn, $checkOut) {
            $query->whereBetween('date', [
                $checkIn,
                date('Y-m-d', strtotime($checkOut . ' -1 day'))
            ]);
        })
        ->take($roomsRequested)
        ->pluck('id'); // just return IDs

    if ($availableUnits->count() >= $roomsRequested) {
        $totalPrice = $pricePerNight * $roomsRequested * $numberOfNights;

        return [
            'success'       => true,
            'message'       => 'Rooms available',
            'total_price'   => $totalPrice,
            'unit_ids'      => $availableUnits, // ✅ return available unit IDs
        ];
    }

    // if not available
    $unitIds = HotelRoomUnit::where('hotel_room_id', $room_id)->pluck('id');

    $unavailableDates = RoomAvailability::whereIn('hotel_room_unit_id', $unitIds)
        ->whereBetween('date', [
            $checkIn,
            date('Y-m-d', strtotime($checkOut . ' -1 day'))
        ])
        ->where('available', false)
        ->pluck('date')
        ->unique()
        ->values();

    return [
        'success'            => false,
        'message'            => 'Some dates are not available',
        'unavailable_dates'  => $unavailableDates
    ];
}


 

public function bookHotelPayment(Request $request, FIBPaymentService $service)
{
    $result = self::checkAvailability(
        $request->hotel_id,
        $request->room_id,
        $request
    );


    if ($result['success'] === false) {
        return response()->json($result, 400);
    }

      $paymentPayload = [
        'monetaryValue' => [
            'amount' => $result['total_price'],
            'currency' => 'IQD',
        ],
        'description' => 'Booking Payment',
        'statusCallbackUrl' => "https://77f7a44e6d74.ngrok-free.app/api/callback/hotel",
        'expiresIn' => 'PT2H',
        'refundableFor' => 'PT48H',
        'category' => 'ECOMMERCE',
    ];

   
    $response = $service->createPayment($paymentPayload);
   HotelPayment::create([
    'user_id'   => auth()->id(),
    'hotel_id'  => $request->hotel_id,
    'room_id'   => $request->room_id,
    'unit_ids'   => $result['unit_ids'],
    'check_in'  => $request->check_in,
    'check_out' => $request->check_out,
    'fib_payment_id'=>$response['paymentId'],
    'price'     => $result['total_price'],
]);
    return response()->json($response);
}


public function bookHotel($hotel_id, $room_id, Request $request,FIBPaymentService $service)
{
   $validated = $request->validate([
    'check_in'   => 'required|date|after_or_equal:today',
    'check_out'  => 'required|date|after:check_in',
    'guests'     => 'required|integer|min:1',
    'rooms'      => 'required|integer|min:1', 
]);
$checkIn  = $validated['check_in'];
$checkOut = $validated['check_out'];
$roomsRequested = $validated['rooms'];


$numberOfNights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));


$hotelRoom = HotelRoom::findOrFail($room_id);
$pricePerNight = $hotelRoom->price;


$availableUnits = HotelRoomUnit::where('hotel_room_id', $room_id)
    ->whereDoesntHave('availabilities', function ($query) use ($checkIn, $checkOut) {
        $query->whereBetween('date', [$checkIn, date('Y-m-d', strtotime($checkOut . ' -1 day'))]);
    })
    ->take($roomsRequested)
    ->get();
 
    if ($availableUnits->count() >= $roomsRequested) {
     $totalPrice = $pricePerNight * $roomsRequested * $numberOfNights;
return response()->json([
    "success"       =>  true,
    'message'            => 'rooms available',
    'requested_rooms'    => $roomsRequested,
    'available_rooms'    => $availableUnits->count(),
    'price'              =>$totalPrice.''.'IQD'
], 200);
   
}


$unitIds = HotelRoomUnit::where('hotel_room_id', $room_id)->pluck('id');

$unavailableDates = RoomAvailability::whereIn('hotel_room_unit_id', $unitIds)
    ->whereBetween('date', [$checkIn, date('Y-m-d', strtotime($checkOut . ' -1 day'))])
    ->where('available', false)
    ->pluck('date')
    ->unique()
    ->values();

return response()->json([
    "success"       =>  false,
    'message'            => 'Not enough rooms available',
    'requested_rooms'    => $roomsRequested,
    'available_rooms'    => $availableUnits->count(),
    'unavailable_dates'  => $unavailableDates,
], 422);      
}

public function callbackHotel(Request $request)
{
    $payload = $request->all();

    $paymentId = $payload['id'] ?? null;
    $status = $payload['status'] ?? null;

    if (!$paymentId || !$status) {
        return response()->json(['error' => 'Invalid callback payload'], 400);
    }

    DB::beginTransaction();

    try {
        $payment = HotelPayment::where('fib_payment_id', $paymentId)->lockForUpdate()->first();

        if (!$payment) {
            DB::rollBack();
            return response()->json(['error' => 'Payment not found.'], 404);
        }
        $payment->update([
            'status' => "success",
        ]);

        $start = Carbon::parse($payment->check_in);
        $end   = Carbon::parse($payment->check_out)->subDay();

        $dates = [];
        foreach ($payment->unit_ids as $unitId) {
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $dates[] = [
                    'hotel_room_unit_id' => $unitId,
                    'date' => $date->format('Y-m-d'),
                    'available' => true,
                ];
            }
        }
        RoomAvailability::insert($dates);

        $bookings = [];
        foreach ($payment->unit_ids as $unitId) {
        $bookings[] = [
        'user_id'        => $payment->user_id,
        'hotel_id'       => $payment->hotel_id,
        'room_id'        => $payment->room_id,
        'unit_id'        => $unitId,
        'amount'         => $payment->price,
        'status'         => 'confirmed',
        'payment_status' => $status,
        'payment_method' => 'FIB',
        'transaction_id' => $paymentId,
        'booking_date'   => now(),
        'start_time'     => $payment->check_in,
        'end_time'       => $payment->check_out,
        'notes'          => 'Hotel booking via FIB',
        'created_at'     => now(),
        'updated_at'     => now(),
        ];
        }
        Booking::insert($bookings);


        DB::commit();
        return response()->json(['message' => 'Callback processed successfully.'], 200);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Failed to process callback.',
            'details' => $e->getMessage()
        ], 500);
    }
}

}
