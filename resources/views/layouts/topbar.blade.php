<header id="page-topbar">
  <div class="layout-width">
    <div class="navbar-header">
      <div class="d-flex">
        <!-- LOGO -->
        <div class="navbar-brand-box horizontal-logo">
          <a href="index" class="logo logo-dark">
            <span class="logo-sm">
              <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
              <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" height="17">
            </span>
          </a>
          <a href="index" class="logo logo-light">
            <span class="logo-sm">
              <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
              <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" height="17">
            </span>
          </a>
        </div>

        <button type="button" class="btn btn-sm fs-16 header-item vertical-menu-btn topnav-hamburger px-3" id="topnav-hamburger-icon">
          <span class="hamburger-icon">
            <span></span>
            <span></span>
            <span></span>
          </span>
        </button>
      </div>

      <div class="d-flex align-items-center">

        <div class="dropdown d-md-none topbar-head-dropdown header-item">
          <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bx bx-search fs-22"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
            <form class="p-3">
              <div class="form-group m-0">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                  <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="dropdown topbar-head-dropdown header-item ms-1">
          <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          
           

           
                <img src="{{ URL::asset('assets/images/flags/us.svg') }}" class="rounded" alt="Header Language" height="20">
         
          </button>
          <div class="dropdown-menu dropdown-menu-end">

            <!-- item-->
            <a href="{{ url('index/en') }}" class="dropdown-item notify-item language py-2" data-lang="en" title="English">
              <img src="{{ URL::asset('assets/images/flags/us.svg') }}" alt="user-image" class="me-2 rounded" height="20">
              <span class="align-middle">English</span>
            </a>

           
            <!-- <a href="{{ url('index/ku') }}" class="dropdown-item notify-item language" data-lang="sp" title="Spanish">
              <img src="{{ URL::asset('assets/images/flags/hu.svg') }}" alt="user-image" class="me-2 rounded" height="20">
              <span class="align-middle">Kurdish</span>
            </a>

         
            <a href="{{ url('index/ar') }}" class="dropdown-item notify-item language" data-lang="sp" title="Spanish">
              <img src="{{ URL::asset('assets/images/flags/iq.svg') }}" alt="user-image" class="me-2 rounded" height="20">
              <span class="align-middle">Arabic</span>
            </a> -->
          </div>
        </div>

        <div class="header-item d-none d-sm-flex ms-1">
          <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-toggle="fullscreen">
            <i class='bx bx-fullscreen fs-22'></i>
          </button>
        </div>

        <div class="header-item d-none d-sm-flex ms-1">
          <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
            <i class='bx bx-moon fs-22'></i>
          </button>
        </div>

        {{-- <div class="dropdown ms-sm-3 header-item topbar-user">
          <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center">
              <img class="rounded-circle header-profile-user" src="@if (Auth::user()->avatar != '') {{ URL::asset('images/' . Auth::user()->avatar) }}@else{{ URL::asset('assets/images/logo.jpg') }} @endif" alt="Header Avatar">
              <span class="ms-xl-2 text-start">
                <span class="d-none d-xl-inline-block fw-semibold user-name-text ms-1"></span>
                <span class="d-none d-xl-block fs-12 user-name-sub-text ms-1">Founder</span>
              </span>
            </span>
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <!-- item-->
            <h6 class="dropdown-header">Welcome Anna!</h6>
            <a class="dropdown-item" href="pages-profile"><i class="mdi mdi-account-circle text-muted fs-16 me-1 align-middle"></i> <span class="align-middle">Profile</span></a>
            <a class="dropdown-item" href="auth-lockscreen-basic"><i class="mdi mdi-lock text-muted fs-16 me-1 align-middle"></i> <span class="align-middle">Lock screen</span></a>
            <a class="dropdown-item" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off font-size-16 me-1 align-middle"></i> <span key="t-logout"></span></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </div>
        </div> --}}

        <div class="dropdown ms-sm-3 header-item topbar-user">
          <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center">
                <img class="rounded-circle header-profile-user" src="{{ URL::asset('assets/images/logo.jpg') }} " alt="Header Avatar">
              <span class="ms-xl-2 text-start">
                <span class="d-none d-xl-inline-block fw-semibold user-name-text ms-1"></span>
                <span class="d-none d-xl-block fs-12 user-name-sub-text ms-1"></span>
              </span>
            </span>
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <!-- item-->
            <h6 class="dropdown-header">Welcome </h6>
            <a class="dropdown-item" href=""><i class="mdi mdi-account-circle text-muted fs-16 me-1 align-middle"></i> <span class="align-middle">Profile</span></a>
            {{-- <a class="dropdown-item" href="auth-lockscreen-basic"><i class="mdi mdi-lock text-muted fs-16 me-1 align-middle"></i> <span class="align-middle">Lock screen</span></a> --}}
            <a class="dropdown-item" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off font-size-16 me-1 align-middle"></i> <span key="t-logout">@lang('translation.logout')</span></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>


<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
      </div>
      <div class="modal-body">
        <div class="mt-2 text-center">
          <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#495057,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
          <div class="fs-15 mx-sm-5 mx-4 mt-4 pt-2">
            <h4 class="fw-bold">Are you sure ?</h4>
            <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
          </div>
        </div>
        <div class="d-flex justify-content-center mb-2 mt-4 gap-2">
          <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
        </div>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
