@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; }
    .map-container { 
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .search-container { 
        position: absolute; 
        top: 10px; 
        left: 10px; 
        z-index: 1000; 
        width: calc(100% - 20px); 
        max-width: 500px;
    }
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    .image-preview {
        position: relative;
        width: 120px;
        height: 120px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
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
        background: rgba(255,255,255,0.8);
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #ef4444;
        font-size: 18px;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .image-preview .delete-btn:hover {
        background: white;
        transform: scale(1.1);
    }
    .coordinates-box {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 10px 15px;
        border-radius: 8px;
        position: absolute;
        bottom: 10px;
        right: 10px;
        z-index: 1000;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .coordinates-box span {
        font-weight: 500;
    }
    .instructions {
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.9);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: none;
        margin-bottom: 24px;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0 !important;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #334155;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #3b82f6;
        background-color: transparent;
        border-bottom: 3px solid #3b82f6;
    }
    .tab-content {
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 8px 8px;
        padding: 1.5rem;
        background: #fff;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('title') Hotels @endslot
    @slot('li_end') Create Hotel @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Hotel</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('hotels.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Language Tabs -->
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
                                <label class="form-label">Hotel Name (English) *</label>
                                <input type="text" class="form-control" name="name[en]" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (English) *</label>
                                <textarea class="form-control" name="description[en]" rows="4" required></textarea>
                            </div>
                        </div>
                        
                        <!-- Kurdish Tab -->
                        <div class="tab-pane fade" id="ku" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Hotel Name (Kurdish) *</label>
                                <input type="text" class="form-control" name="name[ku]" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (Kurdish) *</label>
                                <textarea class="form-control" name="description[ku]" rows="4" required></textarea>
                            </div>
                        </div>
                        
                        <!-- Arabic Tab -->
                        <div class="tab-pane fade" id="ar" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Hotel Name (Arabic) *</label>
                                <input type="text" class="form-control" name="name[ar]" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (Arabic) *</label>
                                <textarea class="form-control" name="description[ar]" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City *</label>
                            <select class="form-select" name="city_id" required>
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Map Section -->
                    <div class="mb-4">
                        <label class="form-label">Location on Map *</label>
                        <div class="map-container">
                            <div class="search-container">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="addressSearch" placeholder="Search location...">
                                    <button class="btn btn-primary" type="button" id="searchButton">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="instructions">
                                <i class="bi bi-info-circle me-1"></i> Click on the map to place the hotel location
                            </div>
                            <div class="coordinates-box">
                                Coordinates: <span id="coordinates">33.3128, 44.3615</span>
                            </div>
                            <div id="map"></div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Latitude *</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" value="33.3128" readonly required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Longitude *</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" value="44.3615" readonly required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Accuracy</label>
                                <input type="text" class="form-control" id="accuracy" value="Approximate location" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label class="form-label">Hotel Images</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                        <small class="text-muted">Upload multiple images to showcase your hotel (max 10 images)</small>
                        
                        <div class="image-preview-container mt-3" id="imagePreview"></div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('hotels.index') }}" class="btn btn-light">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Create Hotel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Baghdad
    const map = L.map('map').setView([33.3128, 44.3615], 13);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add marker with draggable option
    const marker = L.marker([33.3128, 44.3615], {
        draggable: true
    }).addTo(map);
    
    // Update form fields and coordinates display
    function updateFormFields(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        document.getElementById('coordinates').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
    
    // Set initial values
    updateFormFields(33.3128, 44.3615);
    
    // Update on marker drag
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateFormFields(position.lat, position.lng);
        map.panTo(position);
    });
    
    // Update on map click
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateFormFields(e.latlng.lat, e.latlng.lng);
    });
    
    // Search functionality
    document.getElementById('searchButton').addEventListener('click', function() {
        const query = document.getElementById('addressSearch').value;
        if (!query) {
            alert('Please enter a location to search');
            return;
        }
        
        // Show loading state
        const searchBtn = document.getElementById('searchButton');
        const originalBtnContent = searchBtn.innerHTML;
        searchBtn.innerHTML = '<i class="bi bi-search me-1"></i> Searching...';
        searchBtn.disabled = true;
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    // Update map and marker
                    map.setView([lat, lon], 15);
                    marker.setLatLng([lat, lon]);
                    updateFormFields(lat, lon);
                    
                    // Set accuracy value
                    if(data[0].display_name) {
                        document.getElementById('accuracy').value = data[0].display_name.split(',')[0];
                    } else {
                        document.getElementById('accuracy').value = "Location found";
                    }
                } else {
                    alert('Location not found. Please try a different search term.');
                }
            })
            .catch(error => {
                console.error('Error searching location:', error);
                alert('Error searching location. Please try again.');
            })
            .finally(() => {
                // Reset button state
                searchBtn.innerHTML = originalBtnContent;
                searchBtn.disabled = false;
            });
    });
    
    // Press Enter in search field
    document.getElementById('addressSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchButton').click();
        }
    });
    
    // Image preview
    const imagePreview = document.getElementById('imagePreview');
    document.getElementById('images').addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        
        for (const file of e.target.files) {
            const reader = new FileReader();
            const preview = document.createElement('div');
            preview.className = 'image-preview';
            
            const img = document.createElement('img');
            const deleteBtn = document.createElement('div');
            deleteBtn.className = 'delete-btn';
            deleteBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
            
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
                
                // Remove file from input
                const files = Array.from(e.target.files);
                const index = files.indexOf(file);
                if (index > -1) {
                    files.splice(index, 1);
                }
                
                const newFileList = new DataTransfer();
                files.forEach(f => newFileList.items.add(f));
                e.target.files = newFileList.files;
            });
        }
    });
});
</script>
@endsection