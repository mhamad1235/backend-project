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
                    {{-- Left Column: User Info --}}
                    <div class="col-md-6">
                        <h5 class="mb-3">User Information</h5>
                        <p><strong>Name:</strong> {{ $booking->user->name }}</p>
                        <p><strong>Phone:</strong> {{ $booking->user->phone }}</p>
                        <p><strong>City:</strong> {{ $booking->user->city->name ?? 'N/A' }}</p>
                    </div>

                    {{-- Right Column: Booking Info --}}
                    <div class="col-md-6">
                        <h5 class="mb-3">Booking Summary</h5>
                        <p><strong>Date:</strong> {{ $booking->booking_date }}</p>
                        <p><strong>Time:</strong> {{ $booking->start_time }} - {{ $booking->end_time ?? 'N/A' }}</p>
                        <p><strong>Amount:</strong> {{ number_format($booking->amount) }} IQD</p>
                       
                        <p>
                            <strong>Payment Status:</strong>
                            <span class="badge bg-{{ $booking->payment_status == 'paid' ? 'success' : ($booking->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </p>
                    </div>

                    {{-- Full width: Units Booked --}}
                    <div class="col-12 mt-4">
                        <h5 class="mb-3">Booked Units</h5>
                        <div class="row">
                            @foreach($booking->units as $unit)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $unit->name }}</h6>
                                            <p class="card-text">
                                                <strong>Room #:</strong> {{ $unit->room_number }}<br>
                                                <strong>Guests:</strong> {{ $unit->room->guest ?? 'N/A' }}<br>
                                                <strong>Bedrooms:</strong> {{ $unit->room->bedroom ?? 'N/A' }}<br>
                                                <strong>Beds:</strong> {{ $unit->room->beds ?? 'N/A' }}<br>
                                                <strong>Baths:</strong> {{ $unit->room->bath ?? 'N/A' }}<br>
                                                <strong>Price:</strong> {{ number_format($unit->room->price) }} IQD<br>
                                                <strong>Room Type:</strong> {{ $unit->room->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Full width: Notes --}}
                    <div class="col-12 mt-4">
                        <h5 class="mb-2">Notes</h5>
                        <p>{{ $booking->notes ?? 'No notes available.' }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 mt-4 d-flex justify-content-between">
                        <a href="{{ route('bookings.index') }}" class="btn btn-light">← Back to List</a>
                      
                    </div>

                </div> {{-- .row --}}
            </div>
        </div>
    </div>
</div>
@endsection
