<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FIBPaymentService;
use App\Models\HotelRoomUnit;
use App\Models\RoomAvailability;
use App\Models\HotelRoom;
use App\Models\HotelPayment;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\DeleteUnpaidBookingJob;
use Illuminate\Support\Str;

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

    $checkIn        = Carbon::parse($validated['check_in'])->toDateString();
    $checkOut       = Carbon::parse($validated['check_out'])->toDateString();
    $checkOutMinus1 = Carbon::parse($checkOut)->subDay()->toDateString();
    $roomsRequested = (int) $validated['rooms'];
    $nights         = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

    $hotelRoom = HotelRoom::where('id', $room_id)
        ->where('hotel_id', $hotel_id)
        ->firstOrFail();

    // Optional: Validate guest count fits
    if ((int) $validated['guests'] > $hotelRoom->guest) {
        return [
            'success' => false,
            'message' => 'Room cannot accommodate this number of guests.',
        ];
    }

    // Get only units that are fully available for all requested days
    $availableUnitIds = HotelRoomUnit::where('hotel_room_id', $room_id)
        ->whereDoesntHave('availabilities', function ($query) use ($checkIn, $checkOutMinus1) {
            $query->whereBetween('date', [$checkIn, $checkOutMinus1])
                  ->where('status', 'unavailable');
        })
        ->pluck('id');

    if ($availableUnitIds->count() >= $roomsRequested) {
        $totalPrice = $hotelRoom->price * $roomsRequested * $nights;

        return [
            'success'     => true,
            'message'     => 'Rooms available',
            'total_price' => $totalPrice,
            'unit_ids'    => $availableUnitIds->take($roomsRequested)->values()->all(),
            'nights'      => $nights,
        ];
    }

    // Not enough available units – return blocked dates for clarity
    $unitIds = HotelRoomUnit::where('hotel_room_id', $room_id)->pluck('id');

    $unavailableDates = RoomAvailability::whereIn('hotel_room_unit_id', $unitIds)
        ->whereBetween('date', [$checkIn, $checkOutMinus1])
        ->where('status', 'unavailable')
        ->pluck('date')
        ->unique()
        ->values();

    return [
        'success'           => false,
        'message'           => 'Some dates are not available for enough units.',
        'unavailable_dates' => $unavailableDates,
    ];
}


    public function bookHotelPayment(Request $request, FIBPaymentService $service)
    {     
        $result = self::checkAvailability(
            $request->hotel_id,
            $request->room_id,
            $request
        );

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        $paymentPayload = [
            'monetaryValue' => [
                'amount'   => $result['total_price'],
                'currency' => 'IQD',
            ],
            'description'       => 'Booking Payment',
            'statusCallbackUrl' => "https://e678e35df59e.ngrok-free.app/api/v1/callback/hotel",
            'expiresIn'         => 'PT2H',
            'refundableFor'     => 'PT48H',
            'category'          => 'ECOMMERCE',
        ];

        $response = $service->createPayment($paymentPayload);
     
        HotelPayment::create([
            'user_id'        => auth()->id(),
            'hotel_id'       => $request->hotel_id,
            'room_id'        => $request->room_id,
            'unit_ids'       => $result['unit_ids'], // casted as array in model
            'check_in'       => $request->check_in,
            'check_out'      => $request->check_out,
            'fib_payment_id' => $response['paymentId'],
            'price'          => $result['total_price'],
            'status'         => 'pending',
        ]);
         DB::beginTransaction();
        try {
            /** @var HotelPayment|null $payment */
            $payment = HotelPayment::where('fib_payment_id', $response['paymentId'])->lockForUpdate()->first();

            if (!$payment) {
                DB::rollBack();
                return response()->json(['error' => 'Payment not found.'], 404);
            }

            // Idempotency: if already processed, return OK
            if ($payment->status === 'success') {
                DB::commit();
                return response()->json(['message' => 'Already processed.'], 200);
            }

            $start   = Carbon::parse($payment->check_in);
            $endIncl = Carbon::parse($payment->check_out)->subDay();
            $nights  = $start->diffInDays($endIncl) + 1;

            $batchToken = Str::uuid()->toString();
            $rows = [];
            foreach ($payment->unit_ids as $unitId) {
                for ($d = $start->copy(); $d->lte($endIncl); $d->addDay()) {
                    $rows[] = [
                        'hotel_room_unit_id' => $unitId,
                        'date'               => $d->toDateString(),
                        'status'             => 'unavailable', // block
                        'batch_token'        => $batchToken,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }
            }

            RoomAvailability::upsert(
                $rows,
                ['hotel_room_unit_id', 'date'],
                ['status', 'updated_at','batch_token']
            );
            $availabilityIds = RoomAvailability::where('batch_token', $batchToken)->pluck('id');

            $blocked = RoomAvailability::whereIn('hotel_room_unit_id', $payment->unit_ids)
                ->whereBetween('date', [$start->toDateString(), $endIncl->toDateString()])
                ->where('status','unavailable')
                ->count();

            $expected = count($payment->unit_ids) * $nights;

            if ($blocked < $expected) {
                DB::rollBack();
                return response()->json([
                    'error'   => 'Inventory conflict: not all nights could be blocked.',
                    'details' => ['expected' => $expected, 'blocked' => $blocked],
                ], 409);
            }

           
            $perUnitAmount = round($payment->price / max(count($payment->unit_ids), 1), 2);

          
            $booking = Booking::create([
            'user_id'        => $payment->user_id,
            'hotel_id'       => $payment->hotel_id,
            'room_id'        => $payment->room_id,
            'amount'         => $payment->price,
            'payment_status' => 'pending',
            'payment_method' => 'FIB',
            'transaction_id' => $response['paymentId'],
            'booking_date'   => now(),
            'start_time'     => $payment->check_in,
            'end_time'       => $payment->check_out,
            'notes'          => 'Hotel booking via FIB',
        ]);
        $booking->units()->attach($payment->unit_ids);
        DeleteUnpaidBookingJob::dispatch($booking->id, $availabilityIds)->delay(now()->addMinutes(2));


            DB::commit();
              
            return response()->json(['message' => $response], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Failed to process callback.',
                'details' => $e->getMessage(),
            ], 500);
        }
       
    }

    public function bookHotel($hotel_id, $room_id, Request $request, FIBPaymentService $service)
    {
        $validated = $request->validate([
            'check_in'   => 'required|date|after_or_equal:today',
            'check_out'  => 'required|date|after:check_in',
            'guests'     => 'required|integer|min:1',
            'rooms'      => 'required|integer|min:1',
        ]);

        $checkIn         = Carbon::parse($validated['check_in'])->toDateString();
        $checkOut        = Carbon::parse($validated['check_out'])->toDateString();
        $checkOutMinus1  = Carbon::parse($checkOut)->subDay()->toDateString();
        $roomsRequested  = (int) $validated['rooms'];
        $guestCount = $request['guests'];


$availableUnits = HotelRoom::where('guest', '>=', $guestCount)
    ->withCount(['units as available_units_count' => function ($query) use ($checkIn, $checkOut) {
        $query->whereDoesntHave('availabilities', function ($q) use ($checkIn, $checkOut) {
            $q->whereBetween('date', [$checkIn, $checkOut])
              ->where('status', 'unavailable');
        });
       }])
    ->having('available_units_count', '>=', $roomsRequested)
    ->get(); 

                $data=[
                'rooms' => $availableUnits,
                'success'          => true,
                'message'          => 'rooms available',
                'requested_rooms'  => $roomsRequested];
                return $this->jsonResponse(true, "Get Hotels", 200, $data);
    }

    public function callbackHotel(Request $request)
    {

        $payload   = $request->all();
        $paymentId = $payload['id']     ?? null;
        $status    = $payload['status'] ?? null;

        if (!$paymentId || !$status) {
            return response()->json(['error' => 'Invalid callback payload'], 400);
        }

        // Accept only paid-like statuses (adjust to your PSP)
        $isPaid = in_array(strtolower($status), ['paid','completed','success','settled'], true);
        if (!$isPaid) {
            HotelPayment::where('fib_payment_id', $paymentId)->update([
                'status' => strtolower($status),
            ]);
            return response()->json(['message' => 'Callback stored (not paid).'], 200);
        }

     
        try {
            $payment = HotelPayment::where('fib_payment_id', $paymentId)->lockForUpdate()->first();
            $bookings = Booking::whereIn('transaction_id', (array) $paymentId)->lockForUpdate()->get();

            if (!$payment ) {
                return response()->json(['error' => 'Payment not found.'], 404);
            }

            // Idempotency: if already processed, return OK
            if ($payment->status === 'success') {
        
                return response()->json(['message' => 'Already processed.'], 200);
            }

            $payment->update(['status' => 'success']);
            foreach ($bookings as $booking) {
             $booking->update(['payment_status' => 'paid']);
            }

            return response()->json(['message' => 'Callback processed successfully.'], 200);
        } catch (\Throwable $e) {
      
            return response()->json([
                'error'   => 'Failed to process callback.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
