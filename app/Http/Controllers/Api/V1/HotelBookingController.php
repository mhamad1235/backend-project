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

        $checkIn         = Carbon::parse($validated['check_in'])->toDateString();
        $checkOut        = Carbon::parse($validated['check_out'])->toDateString();
        $checkOutMinus1  = Carbon::parse($checkOut)->subDay()->toDateString();
        $roomsRequested  = (int) $validated['rooms'];
        $nights          = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

        $hotelRoom       = HotelRoom::findOrFail($room_id);
        $pricePerNight   = $hotelRoom->price;

        // Exclude units that have ANY blocked date (available=false) in the range
        $availableUnitIds = HotelRoomUnit::where('hotel_room_id', $room_id)
            ->whereDoesntHave('availabilities', function ($query) use ($checkIn, $checkOutMinus1) {
                $query->whereBetween('date', [$checkIn, $checkOutMinus1])
                      ->where('status', 'available'); 
            })
            ->limit($roomsRequested)
            ->pluck('id');

        if ($availableUnitIds->count() >= $roomsRequested) {
            $totalPrice = $pricePerNight * $roomsRequested * $nights;

            return [
                'success'     => true,
                'message'     => 'Rooms available',
                'total_price' => $totalPrice,
                'unit_ids'    => $availableUnitIds->values()->all(), // plain array
                'nights'      => $nights,
            ];
        }

        // Explain why not available
        $unitIds = HotelRoomUnit::where('hotel_room_id', $room_id)->pluck('id');

        $unavailableDates = RoomAvailability::whereIn('hotel_room_unit_id', $unitIds)
            ->whereBetween('date', [$checkIn, $checkOutMinus1])
            ->where('status', 'available')
            ->pluck('date')
            ->unique()
            ->values();

        return [
            'success'           => false,
            'message'           => 'Some dates are not available',
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
            'statusCallbackUrl' => "https://c561f6dea7b0.ngrok-free.app/api/callback/hotel",
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

        return response()->json($response);
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

        $nights          = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

        $hotelRoom       = HotelRoom::findOrFail($room_id);
        $pricePerNight   = $hotelRoom->price;

        $availableUnits = HotelRoomUnit::where('hotel_room_id', $room_id)
            ->whereDoesntHave('availabilities', function ($query) use ($checkIn, $checkOutMinus1) {
                $query->whereBetween('date', [$checkIn, $checkOutMinus1])
                      ->where('status', 'available');
            })
            ->limit($roomsRequested)
            ->get();

        if ($availableUnits->count() >= $roomsRequested) {
            $totalPrice = $pricePerNight * $roomsRequested * $nights;

            return response()->json([
                'success'          => true,
                'message'          => 'rooms available',
                'requested_rooms'  => $roomsRequested,
                'available_rooms'  => $availableUnits->count(),
                'price'            => $totalPrice . ' IQD',
            ], 200);
        }

        $unitIds = HotelRoomUnit::where('hotel_room_id', $room_id)->pluck('id');

        $unavailableDates = RoomAvailability::whereIn('hotel_room_unit_id', $unitIds)
            ->whereBetween('date', [$checkIn, $checkOutMinus1])
            ->where('status', 'available')
            ->pluck('date')
            ->unique()
            ->values();

        return response()->json([
            'success'           => false,
            'message'           => 'Not enough rooms available',
            'requested_rooms'   => $roomsRequested,
            'available_rooms'   => $availableUnits->count(),
            'unavailable_dates' => $unavailableDates,
        ], 422);
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

        DB::beginTransaction();
        try {
            /** @var HotelPayment|null $payment */
            $payment = HotelPayment::where('fib_payment_id', $paymentId)->lockForUpdate()->first();

            if (!$payment) {
                DB::rollBack();
                return response()->json(['error' => 'Payment not found.'], 404);
            }

            // Idempotency: if already processed, return OK
            if ($payment->status === 'success') {
                DB::commit();
                return response()->json(['message' => 'Already processed.'], 200);
            }

            $payment->update(['status' => 'success']);

            $start   = Carbon::parse($payment->check_in);
            $endIncl = Carbon::parse($payment->check_out)->subDay();
            $nights  = $start->diffInDays($endIncl) + 1;

            // Build rows to BLOCK dates (available=false)
            $rows = [];
            foreach ($payment->unit_ids as $unitId) {
                for ($d = $start->copy(); $d->lte($endIncl); $d->addDay()) {
                    $rows[] = [
                        'hotel_room_unit_id' => $unitId,
                        'date'               => $d->toDateString(),
                        'available'          => false, // block
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }
            }

            // Upsert (unique key: unit+date) — idempotent & race-safe
            RoomAvailability::upsert(
                $rows,
                ['hotel_room_unit_id', 'date'],
                ['available', 'updated_at']
            );

            // Verify that ALL nights are blocked for ALL units
            $blocked = RoomAvailability::whereIn('hotel_room_unit_id', $payment->unit_ids)
                ->whereBetween('date', [$start->toDateString(), $endIncl->toDateString()])
                ->where('available', false)
                ->count();

            $expected = count($payment->unit_ids) * $nights;

            if ($blocked < $expected) {
                // Conflict: do NOT create bookings here. Handle refund path upstream if needed.
                DB::rollBack();
                return response()->json([
                    'error'   => 'Inventory conflict: not all nights could be blocked.',
                    'details' => ['expected' => $expected, 'blocked' => $blocked],
                ], 409);
            }

            // Create one booking per unit; split the total amount evenly (optional)
            $perUnitAmount = round($payment->price / max(count($payment->unit_ids), 1), 2);

            $bookings = [];
            foreach ($payment->unit_ids as $unitId) {
                $bookings[] = [
                    'user_id'        => $payment->user_id,
                    'hotel_id'       => $payment->hotel_id,
                    'room_id'        => $payment->room_id,
                    'unit_id'        => $unitId,
                    'amount'         => $perUnitAmount,
                    'status'         => 'confirmed',
                    'payment_status' => strtolower($status),
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
                'error'   => 'Failed to process callback.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
