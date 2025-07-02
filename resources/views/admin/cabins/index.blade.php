@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<style>
    .img-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .img-thumbnail:hover {
        transform: scale(1.5);
        z-index: 10;
        position: relative;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('title') Cabins @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">Cabins</h5>
                <a href="{{ route('cabins.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1 align-bottom"></i> Add New
                </a>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <div class="search-box">
                            <input type="text" class="form-control filter-input" placeholder="Search cabins" id="search">
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
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Images</th>
                            <th>Created At</th>
                            <th>City</th>
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
            url: "{{ route('cabins.index') }}",
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
                data: 'name', 
                name: 'name' 
            },
            { 
                data: 'phone', 
                name: 'phone' 
            },
            { 
                data: 'location', 
                name: 'location', 
                orderable: false,
                searchable: false
            },
            { 
                data: 'images', 
                name: 'images', 
                orderable: false,
                searchable: false
            },
            { 
                data: 'created_at', 
                name: 'created_at' 
            },
            { 
                data: 'city', 
                name: 'city' 
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false 
            },
             
        ],
        language: {
            emptyTable: "No cabins found",
            zeroRecords: "No matching cabins",
            processing: '<div class="spinner-border text-primary" role="status"></div> Loading...'
        },
        order: [[5, 'desc']]
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
    
    // Delete handler
    $(document).on('click', '.delete-btn', function() {
        const url = $(this).data('url');
        const cabinId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this cabin?')) {
            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload();
                    toastr.success('Cabin deleted successfully');
                },
                error: function() {
                    toastr.error('Error deleting cabin');
                }
            });
        }
    });
});
</script>
@endsection