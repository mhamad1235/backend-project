<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Events\NewNotificationEvent;
class BookingController extends Controller
{
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
}
