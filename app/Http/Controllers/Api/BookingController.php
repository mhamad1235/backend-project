<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Events\NewNotificationEvent;
use App\Models\UnavailableSlot;
use Carbon\Carbon;
use App\Models\Environment;
use App\Models\Journey;
use FirstIraqiBank\FIBPaymentSDK\Services\FIBPaymentIntegrationService;
use App\Enums\RoleType;

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
            ->with('bus')
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
{

       
    
   $request->validate([
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
  public function first(){
    $client = new \GuzzleHttp\Client();
    $response = $client->post('https://fib.stage.fib.iq/auth/realms/fib-online-shop/protocol/openid-connect/token', [
    'form_params' => [
        'grant_type' => 'client_credentials',
        'client_id' => 'mp-it', 
        'client_secret' => '2d9d9e4b-8b29-4d74-a393-9b9684975512',
    ],
]);

$data = json_decode($response->getBody(), true);
$accessToken = $data['access_token'];

 return response()->json(['access_token' => $accessToken]);
  }
public function second(Request $request)
{
    $userId = 1; 
    $journeyId = $request->input('journey_id');

    $callbackUrl = "https://soft-lies-rule.loca.lt/api/callback";

    $paymentPayload = [
        'monetaryValue' => [
            'amount' => 10000,
            'currency' => 'IQD',
        ],
        'description' => 'Journey Payment',
        'callbackUrl' => $callbackUrl,
        'redirectUri' => 'https://soft-lies-rule.loca.lt/api/fib/payment-complete',
        'expiresIn' => 'PT2H',
        'refundableFor' => 'PT48H',
        'category' => 'ECOMMERCE',
    ];

    $client = new \GuzzleHttp\Client();

    $accessToken = $request->bearerToken();

    $response = $client->post('https://fib.stage.fib.iq/protected/v1/payments', [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
        'json' => $paymentPayload,
    ]);

    $paymentData = json_decode($response->getBody(), true);

    return response()->json($paymentData);
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


public function handleCallback(Request $request)
{
    $payload = $request->all();

    $paymentId = $payload['id'] ?? null;
    $status = $payload['status'] ?? null;

    if (!$paymentId || !$status) {
        return response()->json(['error' => 'Invalid callback payload'], 400);
    }

    try {
        // Implement your callback handling logic
        return response()->json(['message' => 'Callback processed successfully']);
    } catch (Exception $e) {
        return response()->json(['error' => 'Failed to process callback: ' . $e->getMessage()], 500);
    }

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
}
