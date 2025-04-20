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

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="row gx-lg-5">
            <div class="col-xl-12">
              <div class="mt-xl-0 mt-5">
                <div class="d-flex mb-3">
                  <div class="flex-grow-1">
                    <h4>User ID: <a href="{{ route('users.show', $user) }}" class="text-primary">#{{ $user->id }}</a> </h4>
                    <div class="hstack flex-wrap gap-3">
                      <div class="text-muted">
                        Created Date : {{ $user->created_at->format('d M Y') }}
                      </div>

                    </div>
                  </div>
                  <div class="flex-shrink-0">
                    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" title="Actions">
                      <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill"></i></button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        @can('user_edit')
                          <li><a href="{{ route('users.edit', $user) }}" class="dropdown-item change-status-btn"><i class="ri-pencil-fill text-muted me-2 align-bottom"></i> Edit</a></li>
                        @endcan
                        <li class="dropdown-divider"></li>
                        @can('user_delete')
                          <li><a href="javascript:void(0)" data-id="{{ $user->id }}" data-url="{{ route('users.destroy', $user->id) }}" data-fallback-url="{{ route('users.index') }}" class="dropdown-item remove-item-btn delete-btn"><i class="ri-delete-bin-fill text-muted me-2 align-bottom"></i>
                              Delete</a></li>
                        @endcan
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="row justify-content-between">
                  <div class="col-lg-6 p-1">
                    <div class="table-responsive">
                      <table class="table-nowrap table-borderless mb-0 table">
                        <tbody>
                          <tr>
                            <th scope="row">Name :</th>
                            <td>{{ $user->name }}</td>
                          </tr>
                       
                          <tr>
                            <th scope="row">Phone :</th>
                            <td>{{ $user->phone }}</td>
                          </tr>
                       
                        
                        
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>


              </div>
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
          <!-- end card body -->
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->
    </div>
  </div>
@endsection
