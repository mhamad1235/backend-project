<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-topbar="light">

<head>
  <meta charset="utf-8" />
  <title>@yield('title') | {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Apple Zone is an authorized Apple Store in Kurdistan. We provide genuine Apple products including iPhone, iPad, Macbook, iMac, Apple Watch, and accessories." name="description">
  <meta content="Blue Trinity Services" name="author" />
  <meta content="Basit" name="author" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.png') }}">
  @include('layouts.head-css')
</head>

@yield('body')

@yield('content')

@include('layouts.vendor-scripts')
</body>

</html>
