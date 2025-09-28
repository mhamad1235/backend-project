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
    @slot('title') Edit Cabin @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Cabin</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('restaurants.update', $restaurant) }}" enctype="multipart/form-data">
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
                                <input type="text" class="form-control" name="name[en]" value="{{ $restaurant->translate('en')->name}}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (English) *</label>
                                <textarea class="form-control" name="description[en]" rows="4" required>{{ $restaurant->translate('en')->description}}</textarea>
                            </div>
                        </div>
                        
                        <!-- Kurdish Tab -->
                        <div class="tab-pane fade" id="ku" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Hotel Name (Kurdish) *</label>
                                <input type="text" class="form-control" name="name[ku]" value="{{ $restaurant->translate('ku')->name}}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (Kurdish) *</label>
                                <textarea class="form-control" name="description[ku]" rows="4" required>{{ $restaurant->translate('ku')->description}}</textarea>
                            </div>
                        </div>
                        
                        <!-- Arabic Tab -->
                        <div class="tab-pane fade" id="ar" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Hotel Name (Arabic) *</label>
                                <input type="text" class="form-control" name="name[ar]" value="{{ $restaurant->translate('ar')->name}}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (Arabic) *</label>
                                <textarea class="form-control" name="description[ar]" rows="4" required>{{ $restaurant->translate('ar')->description}}</textarea>
                            </div>
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
function deleteImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/admin/images/${imageId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting image');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map with cabin location
    const initialLat = {{ $restaurant->latitude }};
    const initialLng = {{ $restaurant->longitude }};
    
    const map = L.map('map').setView([initialLat, initialLng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add marker at cabin location
    const marker = L.marker([initialLat, initialLng], {
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
    
    // Image preview for new images
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
function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    // Show loading state
    const deleteBtn = document.querySelector(`[onclick="deleteImage(${imageId})"]`);
    const originalHtml = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i>';
    deleteBtn.style.pointerEvents = 'none';

    fetch(`/admin/images/${imageId}/delete`, {
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