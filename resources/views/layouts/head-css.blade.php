@yield('css')

<style>
  .cursor-pointer:hover {
    cursor: pointer !important
  }

  .dataTables_scrollBody {
    overflow: visible !important;
  }

  label.required::after {
    content: "*";
    color: red;
  }
</style>

{{-- select2 --}}
<link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Layout config Js -->
<script src="{{ URL::asset('assets/js/layout.js') }}"></script>
<!-- Bootstrap Css -->
<link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ URL::asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<!-- custom Css-->
<link href="{{ URL::asset('assets/css/custom.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<!-- Sweet Alert-->
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
