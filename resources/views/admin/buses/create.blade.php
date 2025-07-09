@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; }
    .map-container { position: relative; }
    .search-container { 
        position: absolute; 
        top: 10px; 
        left: 50px; 
        z-index: 1000; 
        width: 300px; 
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('li_2') Buses @endslot
    @slot('title') Create New Bus @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Bus</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('buses.store') }}" id="busForm">
                    @csrf
                    
                    <div class="row">
                            <ul class="nav nav-tabs mb-3" id="langTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">
                                <i class="bi bi-translate me-1"></i> English
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ku-tab" data-bs-toggle="tab" data-bs-target="#ku" type="button" role="tab">
                                <i class="bi bi-translate me-1"></i> Kurdish
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">
                                <i class="bi bi-translate me-1"></i> Arabic
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="langTabContent">
                        <!-- English Tab -->
                        <div class="tab-pane fade show active" id="en" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Owner Name (English) *</label>
                                <input type="text" class="form-control" name="name[en]" required>
                            </div>
                            
                        </div>
                        
                        <!-- Kurdish Tab -->
                        <div class="tab-pane fade" id="ku" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">owner Name (Kurdish) *</label>
                                <input type="text" class="form-control" name="name[ku]" required>
                            </div>
                        
                        </div>
                        
                        <!-- Arabic Tab -->
                        <div class="tab-pane fade" id="ar" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">onwer Name (Arabic) *</label>
                                <input type="text" class="form-control" name="name[ar]" required>
                            </div>
                          
                        </div>
                    </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="map-container">
                                <div class="search-container">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="addressSearch" placeholder="Search location...">
                                        <button class="btn btn-primary" type="button" id="searchButton">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="map"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="latitude" class="form-label">Latitude *</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" readonly required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="longitude" class="form-label">Longitude *</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" readonly required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="accuracy" class="form-label">Accuracy</label>
                            <input type="text" class="form-control" id="accuracy" readonly>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                       <div class="mb-3">
    <label for="city_id" class="form-label">Select City</label>
 <select name="city_id" id="city_id" class="form-select" required>
    <option value="">-- Choose City --</option>
    @foreach ($cities as $city)
        <option value="{{ $city['id'] }}" {{ old('city_id') == $city['id'] ? 'selected' : '' }}>
            {{ $city['name'] }}
        </option>
    @endforeach
</select>

</div>

                        
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Create Bus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('map').setView([33.3128, 44.3615], 13); // Default to Baghdad
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add marker
    const marker = L.marker([33.3128, 44.3615], {
        draggable: true
    }).addTo(map);
    
    // Update form fields when marker is moved
    function updateFormFields(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        // Get address from coordinates (reverse geocoding)
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data.display_name) {
                    document.getElementById('address').value = data.display_name;
                }
            });
    }
    
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateFormFields(position.lat, position.lng);
        map.panTo(position);
    });
    
    // Click on map to set location
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateFormFields(e.latlng.lat, e.latlng.lng);
    });
    
    // Search functionality
    document.getElementById('searchButton').addEventListener('click', function() {
        const query = document.getElementById('addressSearch').value;
        if (!query) return;
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    map.setView([lat, lon], 15);
                    marker.setLatLng([lat, lon]);
                    updateFormFields(lat, lon);
                    
                    document.getElementById('accuracy').value = data[0].type;
                } else {
                    alert('Location not found');
                }
            });
    });
    
    // Press Enter in search field
    document.getElementById('addressSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('searchButton').click();
        }
    });
    
    // Initial update
    updateFormFields(33.3128, 44.3615);
});
</script>
@endsection