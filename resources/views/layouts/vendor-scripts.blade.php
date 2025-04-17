<script src="{{ URL::asset('assets/libs/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
{{-- <script src="{{ URL::asset('assets/js/plugins.js') }}"></script> --}}
<!-- Sweet Alerts js -->
<script src="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
{{-- Select2 --}}
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
{{-- Init form validation --}}
<script src="{{ URL::asset('assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>
{{-- @vite('resources/js/echo.js') --}}
{{-- plugins.js content --}}
@yield('script')
@yield('script-bottom')

{{-- sweetalert2 message --}}
@if (Session::has('message'))
  <script>
    Swal.fire({
      customClass: "{{ Session::get('icon') === 'error' ? 'swal-error' : null }}",
      icon: "{{ Session::get('icon') }}",
      title: "{{ Session::get('title') }}",
      text: @json(Session::get('message')),
      timer: "{{ Session::get('icon') === 'error' ? 20000 : (Session::get('timer') ? Session::get('timer') : 1500) }}",
    })
  </script>
@endif

<script>
  $(document).ready(function() {
    // init select2
    $('.select2').select2();

    // Select2 while open focus on search input
    $(document).on('select2:open', () => {
      $('.select2-search__field').focus();
    });

    let selected = $('.select2').data('selected');
    if (selected) {
      $('.select2').val(selected).trigger('change');
    }

    $('.format-number').each(function() {
      const number = cleanNumber($(this).val());
      let formatedNumber = number.replace(/\B(?=(\d{3})+(?!\d))/g, " ,");
      $(this).val(formatedNumber);
    });
  });

  // Only allow numbers
  const cleanNumber = (number) => {
    return number.replace(/[^\d]/g, '');
  }

  const formatNumber = (number) => {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  //Phone Number Config
  // if placeholder is not set, set it or change it
  $('.phone').each(function() {
    $(this).attr('placeholder', '7xx xxx xxxx');
  });

  $('.phone').on('input', function() {
    let phone = $('#phone').val();
    let phoneLength = phone.length;
    let phoneDigits = cleanNumber(phone);
    let phoneLengthWithoutSpace = phoneDigits.length;

    if (phoneLength === 0) {
      return; // No need to do anything if the input is empty
    }

    if (phoneLengthWithoutSpace > 0 && phoneDigits[0] !== '7') {
      $('#phone').val(phoneDigits.substring(0, phoneLengthWithoutSpace - 1)); // Remove the last character
      return;
    }

    let formattedPhone = '';
    for (let i = 0; i < phoneLengthWithoutSpace; i++) {
      if (i === 3 || i === 6) {
        formattedPhone += ' ';
      }
      formattedPhone += phoneDigits[i];
    }

    $('#phone').val(formattedPhone.substring(0, 12)); // Limit to 12 characters (10 digits + 2 spaces)
  });

  $('.number').on("input", function() {
    const number = cleanNumber($(this).val());
    $(this).val(number.replace(/\B(?=(\d{3})+(?!\d))/g, ""));
  });

  $('.format-number').on('input', function() {
    const number = cleanNumber($(this).val());
    let formatedNumber = number.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    $(this).val(formatedNumber);
  });

  $('.format-number').each(function() {
    const number = cleanNumber($(this).val());
    let formatedNumber = number.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    $(this).val(formatedNumber);
  });

  const formatPriceWithCurrency = (price, currencyCode) => {
    if (currencyCode === 'IQD') {
      // Format for IQD: No decimal places, comma-separated thousands
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      }).format(price) + ' IQD';
    } else {
      // Format for USD: No decimal places, prefixed with '$'
      return '$' + new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      }).format(price);
    }
  }

  const formatPrice = (price) => {
    new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(price);
  }

  const refreshSelect2 = () => {
    $('.select2').select2();
    let selected = $('.select2').data('selected');
    if (selected) {
      $('.select2').val(selected).trigger('change');
    }
  }

  // Show Common ajax response message
  const showMessage = (message = "Data has been updated successfully", timer = 1500, icon = "success") => {
    let iconSrc = "";
    if (icon === "success") {
      iconSrc = "https://cdn.lordicon.com/lupuorrc.json";
    } else if (icon === "error") {
      iconSrc = "https://cdn.lordicon.com/tdrtiskw.json";
    }

    Swal.fire({
      html: '<div class="mt-3">' +
        `<lord-icon src="${iconSrc}" trigger="loop" colors="primary:#${icon === "success" ? "0ab39c" : "f06548"},secondary:#${icon === "success" ? "405189" : "f7b84b"}" style="width:120px;height:120px"></lord-icon>` +
        '<div class="mt-4 pt-2 fs-15">' +
        `<h4>${icon === "success" ? "Success" : "Oops...! Something went Wrong !"}</h4>` +
        `<p class="text-muted mx-4 mb-0">${message}</p>` +
        '</div>' +
        '</div>',
      timer: timer,
      showConfirmButton: false,
    });
  }

  //this function is used to delete data that come from databale by passing the destroy url of that model in the button
  $(document).on('click', '.delete-btn', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    const url = $(this).data('url');
    const fallbackUrl = $(this).data('fallback-url');
    Swal.fire({
      html: '<div class="mt-3">' +
        '<lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>' +
        '<div class="mt-4 pt-2 fs-15 mx-5">' +
        '<h4>Are you Sure ?</h4>' +
        '<p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this ?</p>' +
        '</div>' +
        '</div>',
      showCancelButton: true,
      cancelButtonText: "No, cancel !",
      confirmButtonText: 'Yes, Delete It !',
      customClass: {
        confirmButton: 'btn btn-secondary w-xs me-2 mb-1',
        cancelButton: 'btn btn-light w-xs mb-1'
      },
      buttonsStyling: !1
    }).then(function(willDelete) {
      if (willDelete.isConfirmed) {
        $.ajax({
          type: "POST",
          url: url,
          data: {
            _method: 'DELETE',
            _token: '{{ csrf_token() }}'

          },
          success: function(data) {
            showMessage("Data has been deleted successfully", 1000);
            if (fallbackUrl === undefined) {
              table.ajax.reload();
            } else {
              console.log("fallbackUrl", fallbackUrl);
              setTimeout(() => {
                window.location.href = fallbackUrl;
              }, 1000);
            }

          },
          error: function(data) {
            showMessage(data.responseJSON.message, 5000, "error");
          }
        });
      }
    });
  })
</script>
@stack('script')
