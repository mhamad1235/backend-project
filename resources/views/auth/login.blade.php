@extends('layouts.master-without-nav')

@section('content')
  @php
    $showCredsDetails = config('app.env') == 'local' ? true : false;
    $email = null;
    $password = null;
    if ($showCredsDetails) {
        $email = 'admin@' . strtolower(str_replace(' ', '', config('app.name'))) . '.com';
        $password = 'Password@OneStore';
    }
  @endphp
  <div class="auth-page-wrapper pt-5">
    <!-- auth page bg -->
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
      <div class="bg-overlay"></div>

      <div class="shape">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
          <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
        </svg>
      </div>
    </div>

    <!-- auth page content -->
    <div class="auth-page-content">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="mt-sm-5 text-white-50 mb-4 text-center">
              <div>
                <a href="index" class="d-inline-block auth-logo">
                  <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" height="100">
                </a>
              </div>
              {{-- <p class="fs-15 fw-medium mt-3"></p> --}}
            </div>
          </div>
        </div>
        <!-- end row -->

        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card mt-4">

              <div class="card-body p-4">
                <div class="mt-2 text-center">
                  <h5 class="text-primary">Welcome Back !</h5>
                  <p class="text-muted">Sign in to continue to <b>{{ config('app.name') }}</b>.</p>
                </div>
                <div class="mt-4 p-2">
                  <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                      <label for="username" class="form-label">Email <span class="text-danger">*</span></label>
                      <input type="text" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $email) }}" id="username" name="email" placeholder="Enter email">
                      @error('email')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>

                    <div class="mb-3">
                      <div class="float-end">
                        <a href="{{ route('password.update') }}" class="text-muted">Forgot password?</a>
                      </div>
                      <label class="form-label" for="password-input">Password <span class="text-danger">*</span></label>
                      <div class="position-relative auth-pass-inputgroup mb-3">
                        <input type="password" class="form-control password-input @error('password') is-invalid @enderror pe-5" name="password" placeholder="Enter password" id="password-input" value="{{ $password }}">
                        <button class="btn btn-link position-absolute text-decoration-none text-muted password-addon end-0 top-0" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                        @error('password')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>

                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                      <label class="form-check-label" for="auth-remember-check">Remember me</label>
                    </div>

                    <div class="mt-4">
                      <button class="btn btn-success w-100" type="submit">Sign In</button>
                    </div>

                    {{-- <div class="mt-4 text-center">
                      <div class="signin-other-title">
                        <h5 class="fs-13 title mb-4">Sign In with</h5>
                      </div>
                      <div>
                        <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                        <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                        <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                        <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                      </div>
                    </div> --}}
                  </form>
                </div>
              </div>
              <!-- end card body -->
            </div>
            <!-- end card -->

            {{-- <div class="mt-4 text-center">
              <p class="mb-0">Don't have an account ? <a href="register" class="fw-semibold text-primary text-decoration-underline"> Signup </a> </p>
            </div> --}}

          </div>
        </div>
        <!-- end row -->
      </div>
      <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="text-center">
              <p class="text-muted mb-0">&copy;
                <script>
                  document.write(new Date().getFullYear())
                </script> {{ config('app.name') }}. Crafted with <i class="mdi mdi-heart text-danger"></i> by <a href="https://bluetrinityservices.com/" target="_blank" class="text-reset">{{ config('app.company_name') }}</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- end Footer -->
  </div>
@endsection
@section('script')
  <script src="{{ URL::asset('assets/libs/particles.js/particles.js') }}"></script>
  <script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>
  <script src="{{ URL::asset('assets/js/pages/password-addon.init.js') }}"></script>
@endsection
