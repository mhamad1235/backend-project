@extends('layouts.master')

@section('title')
@endsection

@section('content')
  @component('components.breadcrumb')
    @slot('li_1')
    @endslot
    @slot('li_2')
      {{ route('users.index') }}
    @endslot
    @slot('title')
    @endslot
  @endcomponent

  <x-validation-errors />
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <form class="needs-validation" novalidate action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-xl-8">

                <div class="row mb-3">
                  <label for="name" class="col-sm-3 col-form-label">Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    <x-validation-feedback label="Name" />
                  </div>
                </div>
                   <div class="row mb-3">
                  <label for="password" class="col-sm-3 col-form-label">Password</label>
                  <div class="col-sm-9">
                    <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" required>
                    <x-validation-feedback label="password" />
                  </div>
                </div>


                <div class="row mb-3">
                  <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                  <div class="col-sm-9">
                    <input type="tel" class="form-control phone" id="phone" name="phone" value="{{ old('phone') }}" required>
                    <x-validation-feedback label="Phone" />
                  </div>
                </div>

             <div class="row mb-3">
             <label for="dob" class="col-sm-3 col-form-label">Date of Birth</label>
             <div class="col-sm-9">
             <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}" required>
             <x-validation-feedback label="dob" />
             </div>
             </div>
 <div class="row mb-3">
    <label for="city_id" class="col-sm-3 col-form-label">City</label>
    <div class="col-sm-9">
        <select class="form-select" id="city_id" name="city_id" required>
            <option value="">Select City</option>
            @foreach($cities as $city)
                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
        <x-validation-feedback label="city_id" />
    </div>
</div>




                <div class="row justify-content-start">
                  <div class="col-sm-9">
                    <div>
                      <button class="btn btn-primary w-md" type="submit">Submit</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <!-- end card -->
    </div> <!-- end col -->
  </div>
@endsection
