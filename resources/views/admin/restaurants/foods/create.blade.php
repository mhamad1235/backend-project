@extends('layouts.master')

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('li_2') {{ $restaurant->name }} @endslot
    @slot('title') Add Food @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Add Food</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('restaurants.foods.store', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Food Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach(['popular', 'starters', 'mains', 'drinks', 'desserts'] as $category)
                                <option value="{{ $category }}" @selected(old('category') == $category)>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="images" class="form-label">Food Image</label>
                      
                        <input type="file" name="images[]" id="images"  multiple class="form-control">

                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create Food</button>
                        <a href="{{ route('restaurants.foods.index', $restaurant->id) }}" class="btn btn-light">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
