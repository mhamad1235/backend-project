<div class="modal fade static" id="changeStatusModal" tabindex="-1" aria-hidden="true">
  <form method="POST" id="changeStatusForm" action="">
    @csrf
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Change Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="modelStatus" class="form-label">Status</label>
            <select class="select2" id="modelStatus" name="status">
              @foreach ($enumClass::cases() as $status)
                <option value="{{ $status->value }}">{{ $status->getLabelText() }}</option>
              @endforeach
            </select>
          </div>

          <div class="d-none mb-3">
            <label for="rejectReson" class="col-form-label">Reject Reason:</label>
            <textarea class="form-control" name="rejectReason" id="rejectReson" rows="4"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary subm">Submit</button>
        </div>
      </div>
    </div>
  </form>
</div>

@push('script')
  <script>
    let hasRejectReason = false;
    // open change the status
    $('#changeStatusModal').on('show.bs.modal', function(event) {
      const button = event.relatedTarget
      const status = button.getAttribute('data-status');

      // set the status
      $('#modelStatus').val(status);

      // User can't select the current status
      $('#modelStatus option').prop('disabled', false); // Reset all options
      $('#modelStatus option[value="' + status + '"]').prop('disabled', true); // Disable selected option

      // set the form action
      const form = $('#changeStatusForm');
      const url = button.getAttribute('data-url');
      hasRejectReason = button.getAttribute('data-has-reject-reason') == 1 ? true : false;
      form.attr('action', url);
    })

    // show and hide reject reason
    $('#modelStatus').on('change', function() {
      // get the content/text of the selected option
      const selectetStatus = $(this).find('option:selected').text();

      if (hasRejectReason) {
        // make it lowercase
        if (selectetStatus.toLowerCase() == 'rejected') {
          // find parent and remove the class
          $('#rejectReson').parent().removeClass('d-none');
        } else {
          $('#rejectReson').parent().addClass('d-none');
          $('#rejectReson').val('');
        }
      }
    })

    // submit the form
    $('#changeStatusForm').on('submit', function(event) {
      event.preventDefault();

      const form = $(this);
      const url = form.attr('action');
      let data = form.serialize();

      if (!data.includes('status')) {
        data += '&status=0';
      }

      $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function(response) {
          $('#changeStatusModal').modal('hide');
          showMessage("Status changed successfully");

          // check if there is datatable then reload it
          if ($('#datatable').length) {
            table.ajax.reload();
          } else {
            location.reload();
          }
        },
        error: function(error) {
          showMessage(error.responseJSON.message, 20000, "error");
        }
      })
    })
  </script>
@endpush
