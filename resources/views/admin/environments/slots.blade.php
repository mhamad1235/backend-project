@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">Unavailable Time Slots</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlotModal">
                    <i class="ri-add-line me-1 align-bottom"></i> Add Slot
                </button>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Start Date & Time</th>
                                <th>End Date & Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slots as $slot)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('M d, Y g:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('M d, Y g:i A') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-slot" 
                                            data-id="{{ $slot->id }}"
                                            data-start="{{ \Carbon\Carbon::parse($slot->start_time)->format('Y-m-d\TH:i') }}"
                                            data-end="{{ \Carbon\Carbon::parse($slot->end_time)->format('Y-m-d\TH:i') }}">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <form action="{{ route('environments.slots.destroy', [$environment->id, $slot->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Slot Modal -->
<div class="modal fade" id="addSlotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Unavailable Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('environments.slots.store', $environment->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Start Date & Time</label>
                            <input type="datetime-local" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date & Time</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Slot Modal -->
<div class="modal fade" id="editSlotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Unavailable Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSlotForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="slot_id" id="edit_slot_id">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Start Date & Time</label>
                            <input type="datetime-local" name="start_time" id="edit_start" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date & Time</label>
                            <input type="datetime-local" name="end_time" id="edit_end" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Edit slot modal handling
        $('.edit-slot').click(function() {
            var slotId = $(this).data('id');
            var start = $(this).data('start');
            var end = $(this).data('end');
            
            $('#edit_slot_id').val(slotId);
            $('#edit_start').val(start);
            $('#edit_end').val(end);
            
            // Set form action
            $('#editSlotForm').attr('action', '/admin/environments/' + {{ $environment->id }} + '/slots/' + slotId);
            
            $('#editSlotModal').modal('show');
        });
    });
</script>
@endsection