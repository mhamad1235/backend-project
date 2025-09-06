<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class BookingController extends Controller
{
  public function index()
{
    $bookings = Booking::with(['user'])->get();
    return view('admin.bookings.index', compact('bookings'));
}

    public function create(Request $request)
    {
        $bus = Bus::findOrFail($request->bus_id);
        $users = User::all();
        
        return view('admin.bookings.create', compact('bus', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bookable_id' => 'required',
            'bookable_type' => 'required|in:App\Models\Bus',
            'amount' => 'required|numeric|min:0',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $booking = Booking::create([
                'user_id' => $request->user_id,
                'bookable_id' => $request->bookable_id,
                'bookable_type' => $request->bookable_type,
                'amount' => $request->amount,
                'booking_date' => $request->booking_date,
                'start_time' => $request->booking_date . ' ' . $request->start_time,
                'end_time' => $request->end_time ? $request->booking_date . ' ' . $request->end_time : null,
                'notes' => $request->notes,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Handle payment integration here (if needed)
            // This could call a payment service like Stripe
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully');
    }

    public function show(Booking $booking)
    {
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'rejected', 'cancelled', 'completed'])],
            'payment_status' => ['required', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking updated successfully');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully');
    }
    
    // API Endpoint for mobile app
    public function bookBus(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'amount' => 'required|numeric|min:0',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $bus = Bus::find($request->bus_id);
        
        $booking = $user->bookings()->create([
            'bookable_id' => $bus->id,
            'bookable_type' => get_class($bus),
            'amount' => $request->amount,
            'booking_date' => $request->booking_date,
            'start_time' => $request->booking_date . ' ' . $request->start_time,
            'end_time' => $request->end_time ? $request->booking_date . ' ' . $request->end_time : null,
            'notes' => $request->notes,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        
        // Deduct from user's balance if needed
        // $user->balance -= $request->amount;
        // $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking' => $booking
        ]);
    }
public function updateStatus(Request $request, Booking $booking)
{
    $request->validate([
        'status' => ['required', Rule::in(['pending', 'confirmed', 'rejected', 'cancelled', 'completed'])],
        'amount' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string|max:500'
    ]);
    
    $oldStatus = $booking->status;
    $newStatus = $request->status;
    
    // Store original status for error handling
    $originalStatus = $booking->status;
    
    try {
        // Handle payment status changes
        if ($newStatus === 'confirmed' && $oldStatus === 'pending') {
            // Set amount if provided
            if ($request->has('amount')) {
                $booking->amount = $request->amount;
            }
            
            // Set notes if provided
            if ($request->has('notes')) {
                $booking->notes = $request->notes;
            }
            
            // Set payment status to pending
            $booking->payment_status = 'pending';
        } 
        elseif ($newStatus === 'rejected' || $newStatus === 'cancelled') {
            if ($booking->payment_status === 'paid') {
                // Refund user balance
                $user = $booking->user;
                $user->balance += $booking->amount;
                $user->save();
                $booking->payment_status = 'refunded';
            } 
            elseif ($booking->payment_status === 'pending') {
                $booking->payment_status = 'failed';
            }
        } 
        elseif ($newStatus === 'completed') {
            if ($booking->payment_method === 'cash' && $booking->payment_status === 'pending') {
                $booking->payment_status = 'paid';
            }
        }
        
        $booking->status = $newStatus;
        $booking->save();
        
        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'originalStatus' => $originalStatus
        ], 500);
    }
}
}
