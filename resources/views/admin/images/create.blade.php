@extends('layouts.master')

@section('title')
  Add {{ $title }}
@endsection

@section('content')


  <x-validation-errors />

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <form class="needs-validation" novalidate action="{{ route('images.insert') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-lg-10 col-12">

                {{-- Image Upload --}}
                <div class="row mb-3">
                  <label for="image" class="col-sm-3 col-form-label">Upload Image</label>
                  <div class="col-sm-9">
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="file" id="image" required>
                    <x-validation-feedback label="Image" />
                  </div>
                </div>

                {{-- Submit Button --}}
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
    </div> <!-- end col -->
  </div>
@endsection
