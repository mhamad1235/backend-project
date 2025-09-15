@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    /* Add all the CSS styles from above */
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('title') Hotel Details @endslot
@endcomponent

<div class="container my-5">
    <!-- Hotel Header -->
    <div class="hotel-header text-center">
        <h1 class="display-4 fw-bold">{{ $hotel->name }}</h1>
        <p class="lead">{{ $hotel->description }}</p>
        <div class="d-flex justify-content-center align-items-center mt-3">
            <div class="me-3">
            </div>
            <div>
                <i class="bi bi-telephone-fill"></i> {{ $hotel->phone }}
            </div>
        </div>
    </div>

    <!-- Room Cards -->
    <h2 class="mb-4">Available Rooms</h2>
    <div class="row g-4" id="roomsContainer">
        @foreach($hotel->rooms as $room)
        @php
            $roomType = '';
            if($room->room_type_id == 1) $roomType = 'single';
            else if($room->room_type_id == 2) $roomType = 'double';
            else $roomType = 'suite';
        @endphp
        <div class="col-md-6 col-lg-4" data-type="{{ $roomType }}" data-price="{{ $room->price }}">
            <div class="card">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top room-img" alt="{{ $room->name }}">
                    <div class="price-tag">${{ $room->price }}</div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $room->name }}</h5>
                    <p class="card-text">{{ ucfirst($roomType) }} room with comfortable amenities for your stay.</p>
                    
                    <div class="mb-3">
                        <span class="badge-feature"><i class="bi bi-person"></i> {{ $room->guest }} Guest{{ $room->guest > 1 ? 's' : '' }}</span>
                        <span class="badge-feature"><i class="bi bi-door-closed"></i> {{ $room->bedroom }} Bedroom</span>
                        <span class="badge-feature"><i class="bi bi-lightbulb"></i> {{ $room->beds }} Bed{{ $room->beds > 1 ? 's' : '' }}</span>
                        <span class="badge-feature"><i class="bi bi-droplet"></i> {{ $room->bath }} Bathroom</span>
                    </div>
                    
                    <div class="room-features">
                        <div class="feature">
                            <i class="bi bi-wifi"></i>
                            <span>Wi-Fi</span>
                        </div>
                        <div class="feature">
                            <i class="bi bi-tv"></i>
                            <span>TV</span>
                        </div>
                        <div class="feature">
                            <i class="bi bi-snow"></i>
                            <span>AC</span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                      
                        <button class="btn btn-booking"><a href="{{route('hotels.unit',[$hotel->id,$room->id])}}">Check Now</a></button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('script')
<script>
    // Add the JavaScript functionality from above
</script>
@endsection