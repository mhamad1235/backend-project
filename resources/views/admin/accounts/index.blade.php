@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<!-- Bootstrap Bundle JS (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') {{ $title }} @endslot
    @slot('li_2') {{ route('accounts.index') }} @endslot
    @slot('title') Accounts @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">Accounts</h5>
                @can('account_add')
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1 align-bottom"></i> Add New
                    </a>
                @endcan
            </div>

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <div class="search-box">
                            <input type="text" class="form-control filter-input" placeholder="Search accounts" id="search">
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
                            <th>Role</th>
                            <th>Status</th>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('accounts.index') }}",
            data: function (d) {
                d.search = $('#search').val();
            }
        },
      columns: [
    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // for the index column
    { data: 'name', name: 'name' },
    { data: 'phone', name: 'phone' },
    { data: 'role_type', name: 'role_type' },
    { data: 'status', name: 'status' },
    { data: 'created_at', name: 'created_at' },
    { data: 'action', name: 'action', orderable: false, searchable: false }
],
        language: {
            emptyTable: "No accounts found",
            zeroRecords: "No matching accounts"
        }
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
