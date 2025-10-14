<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\RoomAvailability;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
class DeleteUnpaidBookingJob implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bookingId;
    public $availabilityIds;

    public function __construct($bookingId,$availabilityIds)
    {
        $this->bookingId = $bookingId;
        $this->availabilityIds=$availabilityIds;
    }

    public function handle(): void
    {
          \Log::info("Running DeleteUnpaidBookingJob for booking ID: {$this->bookingId}");

        $booking = Booking::find($this->bookingId);

        if (!$booking) {
            \Log::warning("Booking ID {$this->bookingId} not found.");
            return;
        }

        if ($booking->payment_status !== 'pending') {
            \Log::info("Booking ID {$this->bookingId} already paid or not pending.");
            return;
        }

        // Delete associated availability rows
        $deleted = RoomAvailability::whereIn('id', $this->availabilityIds)->delete();
        \Log::info("Deleted {$deleted} availability records for booking ID {$this->bookingId}");

        // Delete the booking itself
        $booking->delete();
        \Log::info("Deleted unpaid booking ID {$this->bookingId}");
    }



     
    
}
