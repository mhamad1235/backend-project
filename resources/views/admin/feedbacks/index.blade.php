<!-- resources/views/admin/feedbacks/index.blade.php -->
@extends('layouts.master')

@section('title', 'Feedback Management')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<style>
    .rating-stars {
        font-size: 18px;
        letter-spacing: 2px;
    }
    .comment-cell {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .comment-cell:hover {
        overflow: visible;
        white-space: normal;
        position: absolute;
        background: white;
        z-index: 10;
        padding: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 4px;
        max-width: 500px;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Dashboard @endslot
    @slot('title') Feedback Management @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Customer Feedback</h5>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2">Visible: </span>
                    <span class="badge bg-secondary">Hidden: </span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="search-box">
                                <input type="text" class="form-control" id="search" placeholder="Search feedback...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="visible">Visible</option>
                                <option value="hide">Hidden</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-light w-100" id="resetFilters">Reset</button>
                        </div>
                    </div>
                </div>
                
                <table id="feedbacksTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Item</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
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
$(document).ready(function() {
    let table = $('#feedbacksTable').DataTable({
        processing: true,
        serverSide: true,
        dom: 'lrtip',
        ajax: {
            url: "{{ route('admin.feedbacks.index') }}",
            data: function(d) {
                d.search = $('#search').val();
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user', name: 'user' },
            { data: 'feedbackable', name: 'feedbackable' },
            { 
                data: 'rating', 
                name: 'rating',
                orderable: false,
                searchable: false
            },
            { 
                data: 'comment', 
                name: 'comment',
                className: 'comment-cell'
            },
            { 
                data: 'status', 
                name: 'status',
                orderable: false
            },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable: "No feedback records found",
            zeroRecords: "No matching feedbacks found",
            processing: '<div class="spinner-border text-primary" role="status"></div> Loading...'
        }
    });

    // Search input handler
    $('#search').on('keyup', function() {
        table.draw();
    });

    // Status filter handler
    $('#statusFilter').on('change', function() {
        table.draw();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#search').val('');
        $('#statusFilter').val('');
        table.draw();
    });

    // Update status handler
    $(document).on('click', '.make-visible, .make-hidden', function() {
        let button = $(this);
        let url = button.data('url');
        let status = button.data('status');
        
        $.ajax({
            url: url,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Reload table to reflect changes
                    table.ajax.reload(null, false);
                    
                    // Show success notification
                    toastr.success(response.message);
                }
            },
            error: function() {
                toastr.error('Error updating status');
            }
        });
    });

    // Delete feedback handler
    $(document).on('click', '.delete-feedback', function() {
        let feedbackId = $(this).data('id');
        let url = "{{ route('admin.feedbacks.destroy', ':id') }}".replace(':id', feedbackId);
        
        if (confirm('Are you sure you want to delete this feedback?')) {
            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload(null, false);
                    toastr.success('Feedback deleted successfully');
                },
                error: function() {
                    toastr.error('Error deleting feedback');
                }
            });
        }
    });
});
</script>
@endsection