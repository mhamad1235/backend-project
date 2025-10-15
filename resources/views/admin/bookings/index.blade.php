@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
    .badge-pending { background-color: #f0ad4e; color: #fff; }
    .badge-confirmed { background-color: #5cb85c; color: #fff; }
    .badge-rejected { background-color: #d9534f; color: #fff; }
    .badge-cancelled { background-color: #6c757d; color: #fff; }
    .badge-completed { background-color: #17a2b8; color: #fff; }
    .badge-paid { background-color: #5cb85c; color: #fff; }
    .badge-failed { background-color: #d9534f; color: #fff; }
    .badge-refunded { background-color: #6c757d; color: #fff; }
    .status-select, .payment-select {
        width: 120px;
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Bookings @endslot
    @slot('title') Manage Bookings @endslot
@endcomponent

<!-- Amount Modal -->
<div class="modal fade" id="amountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Booking Amount (IQD)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="amountForm">
                    @csrf
                    <input type="hidden" id="bookingId" name="booking_id">
                    <div class="mb-3">
                        <label for="amountIQD" class="form-label">Amount (IQD)</label>
                        <input type="number" class="form-control" id="amountIQD" name="amount" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAmount">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">All Bookings</h5>
                <div class="d-flex">
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1 align-bottom"></i> Create Booking
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Booking Date</th>
                            <th>Check_in   &  Check_out</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id }}</td>
                            <td>
                                {{ $booking->user->name }}<br>
                                <small class="text-muted">{{ $booking->user->phone }}</small>
                            </td>
                             <td>
                             
                                {{ $booking->booking_date }}
                            </td>
                            <td>
                             
                                {{ $booking->start_time }} - {{ $booking->end_time}}
                            </td>
                            <td>
                                @if($booking->amount)
                                    {{ number_format($booking->amount) }} IQD
                                @else
                                    <span class="text-danger">Not set</span>
                                @endif
                            </td>
                          
                            <td>
                                @php
                                    $badgeClass = '';
                                    switch($booking->payment_status) {
                                        case 'paid': $badgeClass = 'badge-paid'; break;
                                        case 'failed': $badgeClass = 'badge-failed'; break;
                                        case 'refunded': $badgeClass = 'badge-refunded'; break;
                                        default: $badgeClass = 'badge-pending';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown d-inline-block">
                                  <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">

                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('bookings.show', $booking->id) }}">
                                                <i class="ri-eye-line align-bottom me-2 text-muted"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('bookings.edit', $booking->id) }}">
                                                <i class="ri-pencil-line align-bottom me-2 text-muted"></i> Edit
                                            </a>
                                        </li>
                                        <li class="dropdown-divider"></li>
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item delete-btn" 
                                               data-id="{{ $booking->id }}" 
                                               data-url="{{ route('bookings.destroy', $booking->id) }}">
                                                <i class="ri-delete-bin-line text-muted me-2 align-bottom"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Global variables to track booking status change
let currentBookingId = null;
let newBookingStatus = null;

$(document).ready(function () {
    // Initialize DataTable
    $('#datatable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        language: {
            emptyTable: "No bookings found",
            zeroRecords: "No matching bookings"
        }
    });

    // Status change handler
    $(document).on('change', '.status-select', function() {
        const bookingId = $(this).data('id');
        const newStatus = $(this).val();
        const currentStatus = $(this).find('option:selected').text();
        
        // Store in global variables
        currentBookingId = bookingId;
        newBookingStatus = newStatus;
        
        if (newStatus === 'confirmed') {
            // Show modal for amount input
            $('#amountModal').modal('show');
        } else if (newStatus === 'rejected') {
            // Directly update without amount
            updateBookingStatus(bookingId, newStatus);
        } else {
            // For other statuses
            updateBookingStatus(bookingId, newStatus);
        }
    });

    // Function to update booking status
    function updateBookingStatus(bookingId, newStatus, amount = null, notes = null) {
        const data = {
            _token: '{{ csrf_token() }}',
            status: newStatus,
            amount: amount,
            notes: notes
        };
        
        $.ajax({
            url: `/bookings/${bookingId}/update-status`,
            method: 'PATCH',
            data: data,
            beforeSend: function() {
                toastr.info('Updating status...');
            },
            success: function(response) {
                toastr.success('Booking status updated successfully');
                location.reload(); // Reload to see changes
            },
            error: function(xhr) {
                toastr.error('Error updating booking status: ' + xhr.responseJSON.message);
                // Reset the select to previous value
                $(`.status-select[data-id="${bookingId}"]`).val(response.originalStatus);
            }
        });
    }

    // Handle modal confirmation
    $('#confirmAmount').on('click', function() {
        const amount = $('#amountIQD').val();
        const notes = $('#notes').val();
        
        if (!amount || amount <= 0) {
            toastr.error('Please enter a valid amount in IQD');
            return;
        }
        
        // Close the modal
        $('#amountModal').modal('hide');
        
        // Update the booking with the amount
        if (currentBookingId && newBookingStatus) {
            updateBookingStatus(currentBookingId, newBookingStatus, amount, notes);
        }
        
        // Reset global variables
        currentBookingId = null;
        newBookingStatus = null;
    });

    // Reset modal when hidden
    $('#amountModal').on('hidden.bs.modal', function () {
        $('#amountIQD').val('');
        $('#notes').val('');
        // Reset the select to previous value
        if (currentBookingId) {
            $(`.status-select[data-id="${currentBookingId}"]`).val('pending');
        }
        currentBookingId = null;
        newBookingStatus = null;
    });

    // Delete handler
    $(document).on('click', '.delete-btn', function() {
        const url = $(this).data('url');
        const bookingId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this booking?')) {
            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    toastr.info('Deleting booking...');
                },
                success: function(response) {
                    toastr.success('Booking deleted successfully');
                    location.reload(); // Reload to see changes
                },
                error: function(xhr) {
                    toastr.error('Error deleting booking: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endsection