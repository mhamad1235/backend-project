<!-- ========== App Menu ========== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="app-menu navbar-menu">
  <!-- LOGO -->
  <div class="navbar-brand-box">
    <!-- Dark Logo-->
    <a href="/" class="logo logo-dark">
      <span class="logo-sm">
        <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" height="70">
      </span>
      <span class="logo-lg">
        <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" class="avatar-sm img-thumbnail rounded p-1" height="30">
      </span>
    </a>
    <!-- Light Logo-->
    <a href="/" class="logo logo-light">
      <span class="logo-sm">
        <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" height="60">
      </span>
      <span class="logo-lg">
        <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="" class="avatar-sm img-thumbnail rounded p-1" height="50">
      </span>
    </a>
    <button type="button" class="btn btn-sm fs-20 header-item btn-vertical-sm-hover float-end p-0" id="vertical-hover">
      <i class="ri-record-circle-line"></i>
    </button>
  </div>

  <div id="scrollbar">
    <div class="container-fluid">

      <div id="two-column-menu">
      </div>

      <ul class="navbar-nav" id="navbar-nav">

        {{-- @role('super-admin|admin') --}}
        <li class="nav-item">
          <a class="nav-link menu-link {{ request()->routeIs('root') ? 'active' : '' }}" href="{{ route('root') }}">
            <i class="ri-apps-2-line"></i> <span>Dashboard</span>
          </a>
        </li>

        {{-- cities --}}
        @can('city_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('cities.*') ? 'active' : '' }}" href="{{ route('cities.index') }}">
              <i class="ri-map-pin-line"></i> <span>Cities</span>
            </a>
          </li>
        @endcan

        {{-- banners --}}
        @can('banner_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('banners.*') ? 'active' : '' }}" href="{{ route('banners.index') }}">
              <i class="ri-image-line"></i> <span>Banners</span>
            </a>
          </li>
        @endcan

        {{-- orders --}}
        @can('order_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('orders.*') ? 'collapsed active' : '' }}" href="#sidebarOrders" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarOrders">
              <i class="ri-shopping-cart-2-line"></i> <span>Orders</span>
            </a>
            <div class="menu-dropdown {{ request()->routeIs('orders.*') ? 'show' : '' }} collapse" id="sidebarOrders">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">

                  <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index') && !request()->get('status') ? 'active' : '' }}">
                    <span>All Orders</span>
                    <span class="badge badge-pill bg-info status-badge d-none" id="totalOrders"></span>
                  </a>

                  <a href="{{ route('orders.index', ['status' => 'unpaid']) }}" class="nav-link {{ request()->get('status') == 'unpaid' ? 'active' : '' }}">
                    <span>UnPaid Orders</span>
                    <span class="badge badge-pill bg-info status-badge d-none" id="totalUnPaidOrders"></span>
                  </a>

                  <a href="{{ route('orders.index', ['status' => 'paid']) }}" class="nav-link {{ request()->get('status') == 'paid' ? 'active' : '' }}">
                    <span>Paid Orders</span>
                    <span class="badge badge-pill bg-info status-badge d-none" id="totalPaidOrders"></span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endcan

        {{-- tickets --}}
        @can('ticket_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
              <i class="ri-ticket-line"></i> <span>Tickets</span>
            </a>
          </li>
        @endcan

        {{-- customer --}}
        @can('customer_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
              <i class="ri-group-line"></i> <span>Customers</span>
            </a>
          </li>
        @endcan

        {{-- notifications --}}
        {{-- @can('notification_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('notifications.*') ? 'collapsed active' : '' }}" href="#sidebarNotification" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarNotification">
              <i class="ri-notification-2-line"></i> <span>Notifications</span>
            </a>
            <div class="menu-dropdown {{ request()->routeIs('notifications.*') ? 'show' : '' }} collapse" id="sidebarNotification">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.index') && !request()->get('status') ? 'active' : '' }}">
                    <span>Notification List</span>
                    <span class="badge badge-pill bg-info status-badge d-none" id="totalNotifications"></span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('notifications.index', ['status' => 'pending']) }}" class="nav-link {{ request()->get('status') == 'pending' ? 'active' : '' }}">
                    <span>Pending Notifications</span>
                    <span class="badge badge-pill bg-info status-badge d-none" id="totalPendingNotifications"></span>
                  </a>
                </li>

                @can('notification_add')
                  <li class="nav-item">
                    <a href="{{ route('notifications.create') }}" class="nav-link {{ request()->routeIs('notifications.create') ? 'active' : '' }}">Add Notification</a>
                  </li>
                @endcan
              </ul>
            </div>
          </li>
        @endcan --}}

        {{-- payment methods --}}
        @can('payment_method_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}" href="{{ route('payment-methods.index') }}">
              <i class="ri-wallet-line"></i> <span>Payment Methods</span>
            </a>
          </li>
        @endcan

        {{-- users --}}
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('users.*') ? 'collapsed active' : '' }}" href="#sidebarUsers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsers">
              <i class="ri-user-3-line"></i> <span>User Management</span>
            </a>
            <div class="menu-dropdown {{ request()->routeIs('users.*') ? 'show' : '' }} collapse" id="sidebarUsers">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">Users List</a>
                </li>

                  <li class="nav-item">
                    <a href="{{ route('users.create') }}" class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}">Add User</a>
                  </li>

              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('users.*') ? 'collapsed active' : '' }}" href="#sidebarImages" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsers">
             <i class="ri-image-add-line"></i> <span>Images</span>
            </a>
            <div class="menu-dropdown {{ request()->routeIs('users.*') ? 'show' : '' }} collapse" id="sidebarImages">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="{{ route('images.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">Images List</a>
                </li>
                  <li class="nav-item">
                  <a href="{{ route('images.upload') }}" class="nav-link {{ request()->routeIs('images.upload') ? 'active' : '' }}">Add Images</a>
                </li>



              </ul>
            </div>
          </li>

            <li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('cities.*') ? 'collapsed active' : '' }}" href="#sidebarCity" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('accounts.*') ? 'true' : 'false' }}" aria-controls="cityAccounts">
        <i class="ri-bank-line"></i> <span>City</span>
    </a>
    <div class="menu-dropdown collapse {{ request()->routeIs('accounts.*') ? 'show' : '' }}" id="sidebarCity">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('cities.index') }}" class="nav-link {{ request()->routeIs('accounts.index') ? 'active' : '' }}">City List</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cities.create') }}" class="nav-link {{ request()->routeIs('accounts.create') ? 'active' : '' }}">Add City</a>
            </li>
        </ul>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('accounts.*') ? 'collapsed active' : '' }}" href="#sidebarAccounts" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('accounts.*') ? 'true' : 'false' }}" aria-controls="sidebarAccounts">
        <i class="ri-building-line"></i> <span>Account Management</span>
    </a>
    <div class="menu-dropdown collapse {{ request()->routeIs('accounts.*') ? 'show' : '' }}" id="sidebarAccounts">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.index') ? 'active' : '' }}">Accounts List</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('accounts.create') }}" class="nav-link {{ request()->routeIs('accounts.create') ? 'active' : '' }}">Add Account</a>
            </li>
        </ul>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('buses.*') ? 'collapsed active' : '' }}" href="#sidebarBus" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('buses.*') ? 'true' : 'false' }}" aria-controls="sidebarAccounts">
        <i class="ri-bus-2-line"></i> <span>Bus</span>
    </a>
    <div class="menu-dropdown collapse {{ request()->routeIs('buses.*') ? 'show' : '' }}" id="sidebarBus">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('buses.index') }}" class="nav-link {{ request()->routeIs('buses.index') ? 'active' : '' }}">Bus List</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('buses.create') }}" class="nav-link {{ request()->routeIs('buses.create') ? 'active' : '' }}">Add Bus</a>
            </li>
        </ul>
    </div>
</li>


<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('bookings.*') ? 'collapsed active' : '' }}"
       href="#sidebarbookings" data-bs-toggle="collapse" role="button"
       aria-expanded="{{ request()->routeIs('bookings.*') ? 'true' : 'false' }}"
       aria-controls="sidebarBus">
       <i class="ri-calendar-check-line"></i> <span>bookings</span>
    </a>
    <div class="menu-dropdown collapse {{ request()->routeIs('bookings.*') ? 'show' : '' }}" id="sidebarbookings">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('bookings.index') }}"
                   class="nav-link {{ request()->routeIs('bookings.index') ? 'active' : '' }}">
                   bookings List
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('bookings.create') }}"
                   class="nav-link {{ request()->routeIs('bookings.create') ? 'active' : '' }}">
                   bookings Bus
                </a>
            </li>
        </ul>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('environments.*') ? 'collapsed active' : '' }}"
       href="#sidebarenvironment" data-bs-toggle="collapse" role="button"
       aria-expanded="{{ request()->routeIs('environments.*') ? 'true' : 'false' }}"
       aria-controls="sidebarenvironment">
       <i class="ri-hotel-bed-line"></i> <span>environment</span>
    </a>
    <div class="menu-dropdown collapse {{ request()->routeIs('environments.*') ? 'show' : '' }}" id="sidebarenvironment">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('environments.index') }}"
                   class="nav-link {{ request()->routeIs('environments.index') ? 'active' : '' }}">
                   environment List
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('environments.create') }}"
                   class="nav-link {{ request()->routeIs('environments.create') ? 'active' : '' }}">
                   environment create
                </a>
            </li>
        </ul>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('hotels.*') ? 'collapsed active' : '' }}"
       href="#sidebarhotels" data-bs-toggle="collapse" role="button"
       aria-expanded="{{ request()->routeIs('hotels.*') ? 'true' : 'false' }}"
       aria-controls="sidebarhotels">
       <i class="ri-hotel-bed-line"></i> <span>hotels</span>
    </a>
    <div class="menu-dropdown collapse {{ request()->routeIs('hotels.*') ? 'show' : '' }}" id="sidebarhotels">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('hotels.index') }}"
                   class="nav-link {{ request()->routeIs('hotels.index') ? 'active' : '' }}">
                   hotels List
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('hotels.create') }}"
                   class="nav-link {{ request()->routeIs('hotels.create') ? 'active' : '' }}">
                   hotels create
                </a>
            </li>
        </ul>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('restaurants.*') ? 'collapsed active' : '' }}"
       href="#sidebarrestaurants" data-bs-toggle="collapse" role="button"
       aria-expanded="{{ request()->routeIs('restaurants.*') ? 'true' : 'false' }}"
       aria-controls="sidebarrestaurants">
       <i class="ri-hotel-bed-line"></i> <span>Restaurants</span>
    </a>
    <div class="menu-dropdown collapse {{ request()->routeIs('restaurants.*') ? 'show' : '' }}" id="sidebarrestaurants">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('restaurants.index') }}"
                   class="nav-link {{ request()->routeIs('restaurants.index') ? 'active' : '' }}">
                   restaurants List
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('restaurants.create') }}"
                   class="nav-link {{ request()->routeIs('restaurants.create') ? 'active' : '' }}">
                   restaurants create
                </a>
            </li>
        </ul>
    </div>
</li>


        {{-- roles --}}
        @can('role_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('roles.*') ? 'collapsed active' : '' }}" href="#sidebarRoles" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarRoles">
              <i class="ri-shield-user-line"></i> <span>Role Management</span>
            </a>
            <div class="menu-dropdown {{ request()->routeIs('roles.*') ? 'show' : '' }} collapse" id="sidebarRoles">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}">Roles List</a>
                </li>

                @can('role_add')
                  <li class="nav-item">
                    <a href="{{ route('roles.create') }}" class="nav-link {{ request()->routeIs('roles.create') ? 'active' : '' }}">Add Role</a>
                  </li>
                @endcan
              </ul>
            </div>
          </li>
        @endcan
        {{-- @endrole --}}

        {{-- faqs --}}
        {{-- @can('faqs_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('faqs.*') ? 'active' : '' }}" href="{{ route('faqs.index') }}">
              <i class="ri-question-line"></i> <span>FAQs</span>
            </a>
          </li>
        @endcan --}}

        {{-- public-info --}}
        @can('public_info_view')
          <li class="nav-item">
            <a class="nav-link menu-link {{ request()->routeIs('public-infos.*') ? 'active' : '' }}" href="{{ route('public-infos.index') }}">
              {{-- <i class="ri-question-line"></i> <span>FAQs</span> --}}
              <i class="ri-information-line"></i> <span>Public Info</span>
            </a>
          </li>
        @endcan

        {{-- exchange rates --}}

      </ul>
    </div>
    <!-- Sidebar -->
  </div>
  <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>

@push('script')
  <script>
    $(document).ready(function() {
      // remove d-none class from the badge
      $('.status-badge').removeClass('d-none');
      getSidebarStatusCount()
    });

    const getSidebarStatusCount = () => {
      $.ajax({
        url: "",
        type: "GET",
        dataType: "json",
        success: function(data) {
          $("#totalOrders").html(data.totalOrders);
          $("#totalUnPaidOrders").html(data.totalUnPaidOrders);
          $("#totalPaidOrders").html(data.totalPaidOrders);

          $("#totalThreads").html(data.totalThreads);
          $("#totalPendingThreads").html(data.totalPendingThreads);
          $("#totalPendingReplies").html(data.totalPendingReplies);

          $("#totalNotifications").html(data.totalNotifications);
          $("#totalPendingNotifications").html(data.totalPendingNotifications);
        }
      });
    }
  </script>
@endpush
