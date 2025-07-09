<!-- resources/views/admin/hotels/edit.blade.php -->
@extends('layouts.master')

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('title') Hotels @endslot
    @slot('li_end') Edit Hotel @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Hotel: {{ $hotel->name }}</h5>
            </div>
            
            <div class="card-body">
                <form action="{{ route('hotels.update', $hotel->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Name Translations</label>
                        <div class="border rounded p-3">
                            @foreach(config('app.available_locales') as $locale)
                            <div class="mb-3">
                                <label class="form-label">Name ({{ strtoupper($locale) }})</label>
                                <input type="text" class="form-control" 
                                       name="name[{{ $locale }}]" 
                                       value="{{ $hotel->getTranslation('name', $locale) }}" 
                                       placeholder="Enter name in {{ strtoupper($locale) }}" required>
                            </div>
                            @endforeach
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
                            <label class="form-label">Latitude</label>
                            <input type="text" class="form-control" name="latitude" value="{{ $hotel->latitude }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" class="form-control" name="longitude" value="{{ $hotel->longitude }}" required>
                        </div>
                    </div>
                    
                    <!-- Existing Images -->
                    @if($hotel->images->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Current Images</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($hotel->images as $image)
                            <div class="position-relative">
                                <img src="{{ Storage::disk('s3')->url($image->path) }}" 
                                     class="img-thumbnail" style="width:100px;height:100px;object-fit:cover">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image"
                                        data-id="{{ $image->id }}">
                                    <i class="ri-close-line"></i>
                                </button>
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('.delete-image').click(function() {
            const imageId = $(this).data('id');
            if (confirm('Are you sure you want to delete this image?')) {
                $.ajax({
                    url: "{{ route('hotels.deleteImage', '') }}/" + imageId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.delete-image[data-id="' + imageId + '"]').closest('.position-relative').remove();
                        }
                    }
                });
            }
        });
    });
</script>
@endsection