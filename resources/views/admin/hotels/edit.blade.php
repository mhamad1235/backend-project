<!-- resources/views/admin/hotels/edit.blade.php -->
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
    @slot('title') Hotels @endslot
    @slot('li_end') Edit Hotel @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="alert"></div>
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Hotel: {{ $hotel->name }}</h5>
            </div>
           
            <div class="card-body">
                <form action="{{ route('hotels.update', $hotel->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
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
                                <input type="text" class="form-control" name="name[en]" value="{{ $hotel->translate('en')->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (English) *</label>
                                <textarea class="form-control" name="description[en]" rows="4" required>{{ $hotel->translate('en')->description }}</textarea>
                            </div>
                        </div>
                        
                        <!-- Kurdish Tab -->
                        <div class="tab-pane fade" id="ku" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Hotel Name (Kurdish) *</label>
                                <input type="text" class="form-control" name="name[ku]" value="{{ $hotel->translate('ku')->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (Kurdish) *</label>
                                <textarea class="form-control" name="description[ku]" rows="4" required>{{ $hotel->translate('ku')->description }}</textarea>
                            </div>
                        </div>
                        
                        <!-- Arabic Tab -->
                        <div class="tab-pane fade" id="ar" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Hotel Name (Arabic) *</label>
                                <input type="text" class="form-control" name="name[ar]" value="{{ $hotel->translate('ar')->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (Arabic) *</label>
                                <textarea class="form-control" name="description[ar]" rows="4" required>{{ $hotel->translate('ar')->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ $hotel->phone }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <select class="form-select" name="city_id" required>
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $hotel->city_id == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account</label>
                            <select class="form-select" name="account_id" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $hotel->account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Location</label>
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
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude *</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="{{ $hotel->latitude }}" readonly required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude *</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="{{ $hotel->longitude }}" readonly required>
                        </div>
                    </div>
                    
                    <!-- Existing Images -->
                    @if($hotel->images->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Current Images</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($hotel->images as $image)
                            <div class="image-preview">
                                    <img width="100px" src="{{ Storage::disk('s3')->url($image->path) }}" alt="Hotel image">
                                    <div class="delete-btn" onclick="deleteImage({{ $image->id }})">
                                        <i class="ri-close-line"></i>
                                    </div>
                                </div>
                    
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Add More Images</label>
                        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                        <small class="text-muted">You can select multiple images</small>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hotels.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Hotel</button>
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
document.addEventListener('DOMContentLoaded', function () {
  const initialLat = {{ $hotel->latitude }};
  const initialLng = {{ $hotel->longitude }};

  const map = L.map('map').setView([initialLat, initialLng], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  // If the map container might be inside a tab/collapse, ensure proper sizing:
  setTimeout(() => map.invalidateSize(), 0);

  const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

  function updateFormFields(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
  }

  marker.on('dragend', () => {
    const p = marker.getLatLng();
    updateFormFields(p.lat, p.lng);
    map.panTo(p);
  });

  map.on('click', (e) => {
    marker.setLatLng(e.latlng);
    updateFormFields(e.latlng.lat, e.latlng.lng);
  });

  document.getElementById('searchButton').addEventListener('click', function () {
    const query = document.getElementById('addressSearch').value;
    if (!query) return;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
      .then(r => r.json())
      .then(data => {
        if (data && data.length) {
          const lat = parseFloat(data[0].lat);
          const lon = parseFloat(data[0].lon);
          map.setView([lat, lon], 15);
          marker.setLatLng([lat, lon]);
          updateFormFields(lat, lon);
        } else {
          alert('Location not found');
        }
      })
      .catch(() => alert('Error searching location'));
  });

  document.getElementById('addressSearch').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') document.getElementById('searchButton').click();
  });

  // Image delete buttons already fine in your code; keep as-is
});
function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    // Show loading state
    const deleteBtn = document.querySelector(`[onclick="deleteImage(${imageId})"]`);
    const originalHtml = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i>';
    deleteBtn.style.pointerEvents = 'none';

    fetch(`/admin/images/hotel/${imageId}/delete`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the image preview from DOM
            const imageElement = deleteBtn.closest('.image-preview');
            if (imageElement) {
                imageElement.style.opacity = '0.5';
                setTimeout(() => imageElement.remove(), 300);
            }
            // Show success message (you can use a toast notification instead)
            showNotification('Image deleted successfully', 'success');
        } else {
            alert('Error deleting image: ' + data.message);
            deleteBtn.innerHTML = originalHtml;
            deleteBtn.style.pointerEvents = 'auto';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting image');
        deleteBtn.innerHTML = originalHtml;
        deleteBtn.style.pointerEvents = 'auto';
    });
}

// Helper function for notifications
function showNotification(message, type = 'info') {
    // You can replace this with your preferred notification system
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection