@extends('layouts.master')

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Bookings @endslot
    @slot('title') Booking Details @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Booking #{{ $booking->id }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>User Information</h5>
                        <p><strong>Name:</strong> {{ $booking->user->name }}</p>
                        <p><strong>Phone:</strong> {{ $booking->user->phone }}</p>
                        <p><strong>City:</strong> {{ $booking->user->city->name ?? 'N/A' }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Booking Details</h5>
                        <p><strong>Bookable:</strong> 
                            @if($booking->bookable_type === 'App\Models\Bus')
                                Bus ({{ $booking->bookable->owner_name }})
                            @else
                                {{ class_basename($booking->bookable_type) }}
                            @endif
                        </p>
                        <p><strong>Date:</strong> {{ $booking->booking_date->format('M d, Y') }}</p>
                        <p><strong>Time:</strong> {{ $booking->start_time->format('h:i A') }} - 
                            {{ $booking->end_time ? $booking->end_time->format('h:i A') : 'N/A' }}</p>
                        <p><strong>Amount:</strong> ${{ number_format($booking->amount, 2) }}</p>
                        <p><strong>Status:</strong> <span class="badge bg-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($booking->status) }}
                        </span></p>
                        <p><strong>Payment Status:</strong> <span class="badge bg-{{ $booking->payment_status == 'paid' ? 'success' : ($booking->payment_status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span></p>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <h5>Notes</h5>
                        <p>{{ $booking->notes ?? 'No notes available' }}</p>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <a href="{{ route('bookings.index') }}" class="btn btn-light">Back to List</a>
                        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-primary">Edit Booking</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection