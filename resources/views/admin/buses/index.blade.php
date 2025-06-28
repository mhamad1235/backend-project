@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('li_2') {{ route('buses.index') }} @endslot
    @slot('title') Buses @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">Buses</h5>
                <a href="{{ route('buses.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1 align-bottom"></i> Add New
                </a>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <div class="search-box">
                            <input type="text" class="form-control filter-input" placeholder="Search buses" id="search">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light w-100" id="reset">Reset</button>
                    </div>
                </div>

                <table id="datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Owner</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Address</th>
                            <th>Created At</th>
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
<script>
$(document).ready(function () {
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('buses.index') }}",
            data: function (d) {
                d.search = $('#search').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false 
            },
            { 
                data: 'owner_name', 
                name: 'owner_name' 
            },
            { 
                data: 'phone', 
                name: 'phone' 
            },
            { 
                data: 'location', 
                name: 'location', 
                orderable: false,
                render: function(data, type, row) {
                    // Make location clickable to open in Google Maps
                    return `<a href="https://www.google.com/maps?q=${row.latitude},${row.longitude}" 
                            target="_blank" class="text-primary">
                            <i class="ri-map-pin-line align-middle me-1"></i>
                            View Map
                        </a>`;
                }
            },
            { 
                data: 'address', 
                name: 'address' 
            },
            { 
                data: 'created_at', 
                name: 'created_at' 
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false 
            }
        ],
        language: {
            emptyTable: "No buses found",
            zeroRecords: "No matching buses",
            processing: '<div class="spinner-border text-primary" role="status"></div> Loading...'
        },
        order: [[5, 'desc']] // Default order by created_at descending
    });

    let debounce;
    $('#search').on('keyup', function () {
        clearTimeout(debounce);
        debounce = setTimeout(() => {
            table.ajax.reload();
        }, 500);
    });

    $('#reset').on('click', function () {
        $('#search').val('');
        table.ajax.reload();
    });
});
</script>
@endsection