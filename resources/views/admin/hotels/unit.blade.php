@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('title') Hotel Room Units @endslot
@endcomponent

<div class="container my-5">
    <!-- Hotel Header -->
    <div class="hotel-header text-center mb-5">
        <h1 class="display-4 fw-bold">{{ $hotel->name }}</h1>
        <p class="lead">{{ $hotel->description }}</p>
        <div class="d-flex justify-content-center align-items-center mt-3">
            <i class="bi bi-telephone-fill"></i> {{ $hotel->phone }}
        </div>
    </div>

    <!-- Units -->
    <h2 class="mb-4">Units for {{ $room->name }}</h2>
    <ul class="list-group">
        @foreach($room->units as $unit)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Room #{{ $unit->room_number }}
                @if($unit->is_available)
                    <span class="badge bg-success">Available</span>
                @else
                    <span class="badge bg-danger">Booked</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection
