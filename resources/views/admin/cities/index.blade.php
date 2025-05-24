@extends('layouts.master')
@use('App\Enums\ActiveStatus')

@section('css')
<!--datatable css-->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<!-- Bootstrap Bundle JS (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center border-0">
                <h5 class="card-title flex-grow-1 mb-0">Cities</h5>
                <div class="d-flex flex-wrap gap-1">
                    @can('city_add')
                        <a href="{{ route('cities.create') }}" type="button" class="btn btn-primary create-btn">
                            <i class="ri-add-line me-1 align-bottom"></i> Add New
                        </a>
                    @endcan
                </div>
            </div>
            
            {{-- filters --}}
            <div class="card-body border-end-0 border-start-0 border border-dashed">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="search-box">
                            <input type="text" class="form-control filter-input" placeholder="Search for cities" id="search">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <div class="col-xxl-1 col-sm-4">
                        <button type="button" class="btn btn-light w-100" id="reset">Reset</button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <table id="datatable" class="dt-responsive table-nowrap table-hover table align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Create Date</th>
                            <th>Action</th>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('cities.index') }}",
            type: "GET",
            data: function(d) {
                d.search = $('#search').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    // Display Arabic name if available
                    return row.translations && row.translations.ar ? row.translations.ar.name : data;
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false 
            }
        ],
        language: {
            emptyTable: "No cities found",
            zeroRecords: "No matching cities found"
        }
    });

    // Search with debounce
    let searchTimeout;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 500);
    });

    // Reset button
    $('#reset').on('click', function() {
        $('#search').val('');
        table.ajax.reload();
    });
});
</script>
@endsection