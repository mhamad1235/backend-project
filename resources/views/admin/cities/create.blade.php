@extends('layouts.master')

@section('title')
  Add {{ $title }}
@endsection

@section('content')
  @component('components.breadcrumb')
    @slot('li_1')
      {{ $title }}
    @endslot
    @slot('li_2')
      {{ route('cities.index') }}
    @endslot
    @slot('title')
      Add {{ $title }}
    @endslot
  @endcomponent

  <x-validation-errors />
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <form class="needs-validation" novalidate action="{{ route('cities.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-lg-10 col-12">

                <div class="row mb-3">
                  <label for="name_en" class="col-sm-3 col-form-label">Name in English</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="name_en" name="en[name]" value="{{ old('en.name') }}" placeholder="Enter city name in english" required>
                    <x-validation-feedback label="Name in English" />
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="name_ar" class="col-sm-3 col-form-label">Name in Arabic</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="name_ar" name="ar[name]" value="{{ old('ar.name') }}" placeholder="Enter city name in arabic" required>
                    <x-validation-feedback label="Name in Arabic" />
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="name_ku" class="col-sm-3 col-form-label">Name in Kurdish</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="name_ku" name="ku[name]" value="{{ old('ku.name') }}" placeholder="Enter city name in kurdish" required>
                    <x-validation-feedback label="Name in Kurdish" />
                  </div>
                </div>

                {{-- cost --}}
                <div class="row mb-3">
                  <label for="cost" class="col-sm-3 col-form-label">Cost</label>
                  <div class="col-sm-9">
                    {{-- input-group --}}
                    <div class="input-group">
                      <span class="input-group-text" id="basic-addon1">$</span>
                      <input type="text" class="form-control format-number" min="1" max="5" id="cost" name="cost" value="{{ old('cost') }}" placeholder="Enter city cost" required>
                    </div>
                    <x-validation-feedback label="Cost" />
                  </div>
                </div>

                {{-- is_delivery check box --}}
                <div class="row mb-3">
                  <label for="is_delivery" class="col-sm-3 col-form-label">Available For Delivery</label>
                  <div class="col-sm-9">
                    <div class="form-check checked">
                      <input class="form-check-input" type="checkbox" id="is_delivery" name="is_delivery" value="1" @checked(old('is_delivery'))>
                      <label class="form-check-label" for="is_delivery">Yes</label>
                    </div>
                  </div>
                </div>

                <div class="row justify-content-start mt-3">
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

@section('script')
  <script src="{{ URL::asset('assets/js/lat-lng.init.js') }}"></script>
@endsection
