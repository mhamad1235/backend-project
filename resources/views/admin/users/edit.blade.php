@extends('layouts.master')

@section('title')
  
@endsection

@section('css')
  <link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
  @component('components.breadcrumb')
    @slot('li_1')
     
    @endslot
    @slot('li_2')
    @endslot
    @slot('title')
      Edit 
    @endslot
  @endcomponent
  <x-validation-errors />
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <form class="needs-validation" novalidate action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="col-xl-8">

                <div class="row mb-3">
                  <label for="name" class="col-sm-3 col-form-label">Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    <x-validation-feedback label="Name" />
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="email" class="col-sm-3 col-form-label">Phone</label>
                  <div class="col-sm-9">
                    <input type="phone" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                    <x-validation-feedback label="Email" />
                  </div>
                </div>

               

               

              

   
                <div class="row justify-content-start">
                  <div class="col-sm-9">
                    <div>
                      <button class="btn btn-info w-md" type="submit">Update</button>
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
