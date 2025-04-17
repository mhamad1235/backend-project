<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
  <meta charset="utf-8" />
  <title>@yield('title') | {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Apple Zone is an authorized Apple Store in Kurdistan. We provide genuine Apple products including iPhone, iPad, Macbook, iMac, Apple Watch, and accessories." name="description">
  <meta content="Apple Zone" name="author" />
  <meta name="_token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.png') }}">
  @include('layouts.head-css')
</head>

@section('body')
  @include('layouts.body')
@show
<!-- Begin page -->
<div id="layout-wrapper">
  @include('layouts.topbar')
  @include('layouts.sidebar')
  <!-- ============================================================== -->
  <!-- Start right Content here -->
  <!-- ============================================================== -->
  <div class="main-content">
    <div class="page-content">
      <div class="container-fluid">
        @yield('content')
      </div>
      <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
    @include('layouts.footer')
  </div>
  <!-- end main content-->
</div>
<!-- END layout-wrapper -->

@include('layouts.customizer')

<p  hidden></p>
<p hidden></p>

<!-- JAVASCRIPT -->
@include('layouts.vendor-scripts')
</body>

</html>
