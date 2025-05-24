@extends('layouts.master')

@section('title')

@endsection
    @section('css')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    <!-- Bootstrap Bundle JS (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
<!-- jQuery (required for DataTables) -->

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center gy-3">
            <div class="col-sm">
              <h5 class="card-title mb-0"></h5>
            </div>
            <div class="col-sm-auto">
              <div class="d-flex flex-wrap gap-1">
             
                  <a href="{{ route('users.create') }}" type="button" class="btn btn-primary create-btn"><i class="ri-add-line me-1 align-bottom"></i> Add New</a>
           

              </div>
            </div>
          </div>
        </div>
        {{-- filters --}}
        <div class="card-body border-end-0 border-start-0 border border-dashed">
          <div>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="search-box">
                  <input type="text" class="form-control filter-input" placeholder="Search for users name, email, phone" id="search" oninput="dateChanged()">
                  <i class="ri-search-line search-icon"></i>
                </div>
              </div>
              <!--end col-->
              <div class="col-md-5">
                <div>
                
                  </select>
                </div>
              </div>
              <!--end col-->
              <div class="col-xxl-1 col-sm-4">
                <div>
                  <button type="button" class="btn btn-light w-100" onclick="reset()">Reset</button>
                </div>
              </div>
              <!--end col-->
            </div>
            <!--end row-->
          </div>
        </div>
        <div class="card-body">
          <table id="datatable" class="dt-responsive table-nowrap table-hover table align-middle" style="width:100%">
            <thead class="table-light">
              <tr>
                <th data-ordering="false">ID</th>
                <th>Name</th>
          
                <th>Phone</th>
                <th>date of birth</th>

             
                <th>Create Date</th>
                <th>action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')


  {{-- Datatable Init --}}
  <script>
    let table;

    function dateChanged() {
      table.draw();
    }

    function reset() {
      $('.filter-input').val('').trigger('change')
      table.draw();
    }

    $(function() {
      table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: true,
        lengthMenu: [10, 20, 50, 100],
        pageLength: 10,
        scrollX: true,
        order: [
          [0, "desc"]
        ],
        ajax: {
          url: "{{ route('users.index') }}",
          method: "GET",
          data: function(d) {
            d.roleId = $("#roleFilter").find(":selected").val();
            d.search = $("#search").val();
          }
        },
        columns: [{
            data: 'id'
          },
          {
            data: 'name'
          },
          {
            data: 'phone'
          },
          {
            data: 'dob'
          },
          {
            data: 'created_at',
          },
          {
            data: 'action',
            orderable: false,
            searchable: false
          },
        ]
      })

      // select dropdown for change the page length
      $('.dataTables_length select').addClass('form-select form-select-sm');

      // add margin top to the pagination and info
      $('.dataTables_info, .dataTables_paginate').addClass('mt-3');

      // remove search input
      $('.dataTables_filter').remove();
    });
  </script>
@endsection
