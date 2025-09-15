<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Payment;
use App\Models\JourneyUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Events\NewNotificationEvent;
use App\Models\UnavailableSlot;
use Carbon\Carbon;
use App\Models\Environment;
use App\Models\Journey;
use FirstIraqiBank\FIBPaymentSDK\Services\FIBPaymentIntegrationService;
use App\Enums\RoleType;
use App\Services\FIBPaymentService;
use Illuminate\Support\Facades\Http;
use App\Models\HotelRoomUnit;
use App\Models\RoomAvailability;
use App\Models\HotelRoom;
use App\Models\HotelPayment; 
use Illuminate\Support\Facades\DB;
use App\Models\JourneyRegistrationGroup;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{

    protected $paymentService;
     public function __construct(FIBPaymentIntegrationService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
     public function getBuses(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100', // in kilometers
        ]);

        $radius = $request->radius ?? 10; // default 10km radius
        $buses = Bus::withinRadius(
            $request->latitude,
            $request->longitude,
            $radius
        )->get();

        return response()->json([
            'buses' => $buses
        ]);
    }

public function createBooking(Request $request, Bus $bus)
    {
        $validated = $request->validate([
     
        'booking_date' => 'required|date',
        'start_time' => 'required|date_format:Y-m-d H:i:s',
        'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
        'notes' => 'nullable|string',
 
    ]);

    // ✅ This is the correct way — fills bookable_id and bookable_type
    $booking = $bus->bookings()->create([
        'user_id' => $request->user()->id,
        'booking_date' => $validated['booking_date'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'notes' => $validated['notes'] ?? null,
    ]);
    
    broadcast(new NewNotificationEvent("Data changed at: ".now()));
    return response()->json([
        'message' => 'Bus booked successfully',
        'booking' => $booking,
    ], 201);
    }

    public function getUserBookings(Request $request)
    {
        $bookings = Auth::user()->bookings()
            ->with(['hotel.city','room.type','unit'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'bookings' => $bookings
        ]);
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Booking cannot be canceled'], 400);
        }

        // Refund if paid with balance
        if ($booking->payment_status === 'paid') {
            $user = Auth::user();
            $user->balance += $booking->amount;
            $user->save();
        }

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => $booking->payment_status === 'paid' ? 'refunded' : 'cancelled'
        ]);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }


    public function calculatePrice(Request $request)
{$request->validate([
        'bookable_type' => 'required|string',
        'bookable_id' => 'required|integer',
        'start_time' => 'required|date_format:Y-m-d h:i A',
        'end_time' => 'required|date_format:Y-m-d h:i A|after:start_time',
    ]);

    $startTime = Carbon::createFromFormat('Y-m-d h:i A', $request->start_time);
    $endTime = Carbon::createFromFormat('Y-m-d h:i A', $request->end_time);

    // Calculate total difference in seconds
    $totalSeconds = $startTime->diffInSeconds($endTime);

    // Calculate full days and leftover seconds
    $fullDays = floor($totalSeconds / 86400); // 86400 seconds in a day
    $leftoverSeconds = $totalSeconds % 86400;

    // Convert leftover seconds to hours (decimal)
    $leftoverHours = $leftoverSeconds / 3600;

    // Define your environment and prices
    $environment = Environment::findOrFail($request->bookable_id);

    $hourlyPrices = [
        4 => 100,
        8 => 180,
        12 => 250,
    ];

    $dailyPrice = 400;

    $price = 0;

    if ($fullDays > 0) {
        // Price for full days
        $price += $fullDays * $dailyPrice;

        if ($leftoverHours > 0) {
            // Find smallest tier >= leftoverHours
            $tierHours = collect(array_keys($hourlyPrices))
                ->filter(fn($h) => $h >= $leftoverHours)
                ->sort()
                ->first();

            if ($tierHours) {
                $price += $hourlyPrices[$tierHours];
            } else {
                // leftover hours exceed max tier, charge daily price again
                $price += $dailyPrice;
            }
        }
    } else {
        // Booking less than one day
        $tierHours = collect(array_keys($hourlyPrices))
            ->filter(fn($h) => $h >= $leftoverHours)
            ->sort()
            ->first();

        if ($tierHours) {
            $price = $hourlyPrices[$tierHours];
        } else {
            $price = $dailyPrice;
        }
    }
    
return $price;

    }
public function store(Request $request)
{

    try {
    $request->validate([
        'bookable_type' => 'required|string',
        'bookable_id' => 'required|integer',
        'start_time' => 'required|date_format:Y-m-d h:i A',
        'end_time' => 'required|date_format:Y-m-d h:i A|after:start_time',
    ]);

    $bookableType = $request->bookable_type;
    $bookableId = $request->bookable_id;

    // Parse input datetimes to 24-hour format strings
    $startTime = Carbon::createFromFormat('Y-m-d h:i A', $request->start_time)->format('Y-m-d H:i:s');
    $endTime = Carbon::createFromFormat('Y-m-d h:i A', $request->end_time)->format('Y-m-d H:i:s');

    // Check overlapping bookings
    $overlapBooking = Booking::where('bookable_type', $bookableType)
        ->where('bookable_id', $bookableId)
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_time', [$startTime, $endTime])
                ->orWhereBetween('end_time', [$startTime, $endTime])
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                });
        })->exists();

    // Check overlapping unavailable slots
    $overlapUnavailable = UnavailableSlot::where('bookable_type', $bookableType)
        ->where('bookable_id', $bookableId)
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_time', [$startTime, $endTime])
                ->orWhereBetween('end_time', [$startTime, $endTime])
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                });
        })->exists();

    if ($overlapBooking || $overlapUnavailable) {
        return response()->json([
            'message' => 'The selected time slot is not available for booking.'
        ], 400);
    }

    Booking::create([
        'bookable_type' => $bookableType,
        'bookable_id' => $bookableId,
        'user_id' => 1,
        'start_time' => $startTime,
        'end_time' => $endTime,
    ]);

    return response()->json(['message' => 'Booking created successfully.','prce'=>$this->calculatePrice($request)], 201);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Validation failed: ' . $e->getMessage()], 422);
    }
}
//   public function first(){
//     $client = new \GuzzleHttp\Client();
//     $response = $client->post('https://fib.stage.fib.iq/auth/realms/fib-online-shop/protocol/openid-connect/token', [
//     'form_params' => [
//         'grant_type' => 'client_credentials',
//         'client_id' => 'mp-it', 
//         'client_secret' => '2d9d9e4b-8b29-4d74-a393-9b9684975512',
//     ],
// ]);

// $data = json_decode($response->getBody(), true);
// $accessToken = $data['access_token'];

//  return response()->json(['access_token' => $accessToken]);
//   }


public function second(Request $request, $id, FIBPaymentService $service)
{
    $journey = Journey::findOrFail($id);
    $userId  = Auth::id();

    $data = $request->validate([
        'type'            => 'required|in:family,group',
        'adults_count'    => 'required_if:type,family|nullable|integer|min:1|max:100',
        'children_count'  => 'required_if:type,family|nullable|integer|min:0|max:100',
        'people_count'    => 'required|integer|min:1|max:100',
        'contact_user_id' => 'nullable|integer|exists:users,id',
    ]);

    $isFamily = $data['type'] === 'family';
    if ($isFamily) {
        $adults = (int)($data['adults_count'] ?? 1);
        $children = (int)($data['children_count'] ?? 0);
        $people_count=(int)($data['people_count'] ?? 0);
        $totalPeople = $adults + $children+$people_count;
    } else {
        $totalPeople = (int)($data['people_count'] ?? 1);
        $adults = 0;
        $children = 0;
    }
    if ($totalPeople < 1) {
        throw ValidationException::withMessages(['type' => ['Total headcount must be at least 1.']]);
    }

    $contactUserId = $data['contact_user_id'] ?? $userId;
    $already = JourneyRegistrationGroup::where('journey_id', $journey->id)
        ->where('contact_user_id', $contactUserId)->exists();
    if ($already) {
        throw ValidationException::withMessages(['contact_user_id' => ['You already registered for this journey.']]);
    }

    $amount = (int)$journey->price * $totalPeople;

  


    $paymentPayload = [
        'monetaryValue' => ['amount' => $amount, 'currency' => 'IQD'],
        'description'       => 'Journey Payment',
        'statusCallbackUrl' => 'https://60e82ee842bd.ngrok-free.app/api/callback',
        'expiresIn'         => 'PT2H',
        'refundableFor'     => 'PT48H',
        'category'          => 'ECOMMERCE',
     
    ];

    $fib = $service->createPayment($paymentPayload);

    $payment = Payment::create([
        'paymentable_id'   => $journey->id,
        'paymentable_type' => Journey::class,
        'user_id'          => $contactUserId,
        'fib_payment_id'   => $fib['paymentId'] ?? null,
        'amount'           => $amount,
        'currency'         => 'IQD',
        'status'           => 'pending',
        'request_payload'  => $paymentPayload,
        'fib_response'     => $fib,
        'meta'             => [
            'journey_id'     => $journey->id,
            'type'           => $isFamily ? 'family' : 'group',
            'adults_count'   => $adults,
            'children_count' => $children,
            'total_people'   => $totalPeople,
            'contact_user_id'=> $contactUserId,
        ],
    ]);

    return response()->json([
        'message'        => 'Payment created.',
        'payment_id'     => $payment->id,
        'fib_payment_id' => $payment->fib_payment_id,
        'status'         => $payment->status,
        'amount'         => $amount,
        'currency'       => 'IQD',
        'redirect'       => $fib['paymentUrl'] ?? null,
         'fib_response'  =>$fib
    ], 201);
}
  public function third($paymentId)
{
    $client = new \GuzzleHttp\Client();

    // Step 1: Get access token
    $tokenResponse = $client->post('https://fib.stage.fib.iq/auth/realms/fib-online-shop/protocol/openid-connect/token', [
        'form_params' => [
            'grant_type' => 'client_credentials',
            'client_id' => 'mp-it',
            'client_secret' => '2d9d9e4b-8b29-4d74-a393-9b9684975512',
        ],
    ]);

    $accessToken = json_decode($tokenResponse->getBody(), true)['access_token'];

    // Step 2: Get payment status
    $response = $client->get("https://fib.stage.fib.iq/protected/v1/payments/{$paymentId}", [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ],
    ]);

    $data = json_decode($response->getBody(), true);

    return response()->json($data);
}
public function refund($paymentId)
{
    $client = new \GuzzleHttp\Client();

    // Step 1: Get access token (you can reuse your logic from first() or third())
    $tokenResponse = $client->post('https://fib.stage.fib.iq/auth/realms/fib-online-shop/protocol/openid-connect/token', [
        'form_params' => [
            'grant_type' => 'client_credentials',
            'client_id' => 'mp-it',
            'client_secret' => '2d9d9e4b-8b29-4d74-a393-9b9684975512',
        ],
    ]);
    
    $accessToken = json_decode($tokenResponse->getBody(), true)['access_token'];

    // Step 2: Send refund request
    $response = $client->post("https://fib.stage.fib.iq/protected/v1/payments/{$paymentId}/refund", [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ],
    ]);

    $data = json_decode($response->getBody(), true);

    return response()->json($data);
}


public function callback(Request $request)
{
    // 1) Verify signature from FIB (HMAC, headers, etc.) – depends on provider docs
    // abort_if(! $this->isValidSignature($request), 403);

    $payload = $request->all();
    $paymentId = $payload['id']     ?? null;
    $status    = $payload['status'] ?? null;// e.g., SUCCEEDED/FAILED

    $payment = Payment::where('fib_payment_id', $paymentId)->lockForUpdate()->first();
    if (! $payment) {
        return response()->json(['message' => 'Payment not found'], 404);
    }

    // Idempotency: if already processed, exit
    if (in_array($payment->status, ['succeeded','failed','expired','refunded'])) {
        return response()->json(['message' => 'Already processed'], 200);
    }

    // Update raw response
    $payment->fib_response = array_merge((array)$payment->fib_response, ['callback' => $payload]);
 
    if ($status === 'PAID') {
        DB::transaction(function () use ($payment) {
            $meta = $payment->meta ?? [];
 
                JourneyRegistrationGroup::create([
                    'journey_id'      => $meta['journey_id'],
                    'contact_user_id' => $meta['contact_user_id'],
                    'type'            => $meta['type'],
                    'adults_count'    => $meta['adults_count'],
                    'children_count'  => $meta['children_count'],
                    'total_people'    => $meta['total_people'],
                    'paid'            => true,
                    'status'          => 'confirmed',
                ]);
            

            $payment->status = 'success';
            $payment->save();
        });

        return response()->json(['message' => 'OK'], 200);
    }

    $payment->status = match ($status) {
        'failed'  => 'failed',
        'expired' => 'expired',
        default   => 'pending',
    };
    $payment->save();

    return response()->json(['message' => 'OK'], 200);
}




public function payment(Request $request){
    
}
    public function touristDashboard($id){
        try {
       $journey = Journey::findOrFail($id); 
        $this->authorize('view', $journey); 


        if ($journey) {
        return response()->json([
            'message' => 'Welcome to the Tourist dashboard!',
              'user' => $journey 
        ]);
    }
        } catch (\Throwable $th) {
           return response()->json([
            'message' => 'Journey not found or you do not have permission to view it.', 
           ], 404);
        }
    }
    public function payout(Request $request,FIBPaymentService $service){
    return $service->getAccessToken();
    }
    

    public function authorizePayout(string $payoutId, FIBPaymentService $service)
{
    // Your bearer token from FIB authentication
    $token = $service->getAccessToken();

    $url = "https://fib.stage.fib.iq/protected/v1/payouts/{$payoutId}/authorize";

    $response = Http::withToken($token)->post($url);

    if ($response->successful()) {
        return [
            'success' => true,
            'message' => 'Payout authorized successfully'
        ];
    }

    return [
        'success' => false,
        'status'  => $response->status(),
        'error'   => $response->body()
    ];
}
public function rejectJourney(string $paymentId, FIBPaymentService $service, $journeyId)
{
    $paymentData = $service->getPayment($paymentId);

    if (($paymentData['status'] ?? '') !== 'PAID') {
        return response()->json([
            'success' => false,
            'message' => 'Payment is not completed yet',
            'status'  => $paymentData['status'] ?? 'UNKNOWN'
        ], 400);
    }

    $iban = $paymentData['paidBy']['iban'] ?? null;
    if (!$iban) {
        return response()->json([
            'success' => false,
            'message' => 'No IBAN found in payment data'
        ], 400);
    }

    $payoutResult = $service->createPayout(
        5000,
        'IQD',
        $iban,
        'Auto payout after payment'
    );

    if (!isset($payoutResult['payoutId'])) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create payout',
            'data'    => $payoutResult
        ], 400);
    }

    $authorizeData = $service->authorizePayout($payoutResult['payoutId']);
    if (!$authorizeData['success']) {
        return response()->json([
            'success'   => false,
            'message'   => 'Payout authorization failed',
            'payment'   => $paymentData,
            'payout'    => $payoutResult,
            'authorize' => $authorizeData
        ], 400);
    }

    $record = JourneyUser::where('journey_id', $journeyId)
        ->where('user_id', Auth::id())
        ->first();

    if ($record) {
        $record->delete();
    }

    return response()->json([
        'success'   => true,
        'message'   => 'Journey user removed successfully',
        'payment'   => $paymentData,
        'payout'    => $payoutResult,
        'authorize' => $authorizeData
    ]);
}






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
        foreach ($payment->unit_ids as $unitId) {
            Booking::create([
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
            ]);
        }

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
