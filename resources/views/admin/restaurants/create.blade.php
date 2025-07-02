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
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    .image-preview {
        position: relative;
        width: 100px;
        height: 100px;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255,255,255,0.7);
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('title') Create Restaurant @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Restaurant</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('restaurants.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Restaurant Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
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
                        
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="city_id" class="form-label">City *</label>
                            <select name="city_id" id="city_id" class="form-select" required>
                                <option value="">-- Choose City --</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">
                                        {{ $city->getTranslation('name', 'en')->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label">Restaurant Image</label>
                            <input type="file" name="images[]" multiple>
                            
                            <div class="image-preview-container" id="imagePreview"></div>
                        </div>
                        
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Create Restaurant</button>
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
    const map = L.map('map').setView([33.3128, 44.3615], 13);
    
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
                    const result = data[0];
                    const lat = parseFloat(result.lat);
                    const lon = parseFloat(result.lon);
                    
                    map.setView([lat, lon], 15);
                    marker.setLatLng([lat, lon]);
                    updateFormFields(lat, lon);
                    
                    // Update address field
                    document.getElementById('address').value = result.display_name;
                    document.getElementById('accuracy').value = result.type;
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
    
    // Image preview
    const imagePreview = document.getElementById('imagePreview');
    document.getElementById('image').addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const reader = new FileReader();
            const preview = document.createElement('div');
            preview.className = 'image-preview';
            
            const img = document.createElement('img');
            const deleteBtn = document.createElement('div');
            deleteBtn.className = 'delete-btn';
            deleteBtn.innerHTML = '<i class="ri-close-line"></i>';
            
            preview.appendChild(img);
            preview.appendChild(deleteBtn);
            imagePreview.appendChild(preview);
            
            reader.onload = function(e) {
                img.src = e.target.result;
            }
            
            reader.readAsDataURL(file);
            
            // Remove preview on delete button click
            deleteBtn.addEventListener('click', function() {
                preview.remove();
                e.target.value = '';
            });
        }
    });
});
</script>
@endsection