@extends('layouts.master')

@section('title')
  @lang('translation.dashboards')
@endsection

@section('css')
  <link href="{{ URL::asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ URL::asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
  <div class="row">
    <div class="col-xl-4">
      <div class="card card-animate">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar-sm flex-shrink-0">
              <span class="avatar-title bg-primary-subtle text-primary rounded-2 fs-2">
                <i data-feather="users" class="text-primary"></i>
              </span>
            </div>
            <div class="flex-grow-1 ms-3 overflow-hidden">
              <p class="text-uppercase fw-semibold text-muted text-truncate mb-3">Total Users</p>
              <h4 class="fs-4 mb-0">1234</h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4">
      <div class="card card-animate">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar-sm flex-shrink-0">
              <span class="avatar-title bg-warning-subtle text-warning rounded-2 fs-2">
                <i data-feather="award" class="text-warning"></i>
              </span>
            </div>
            <div class="flex-grow-1 ms-3">
              <p class="text-uppercase fw-semibold text-muted text-truncate mb-3">Total Tickets</p>
              <h4 class="fs-4 mb-0">5678</h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4">
      <div class="card card-animate">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar-sm flex-shrink-0">
              <span class="avatar-title bg-info-subtle text-info rounded-2 fs-2">
                <i data-feather="dollar-sign" class="text-info"></i>
              </span>
            </div>
            <div class="flex-grow-1 ms-3 overflow-hidden">
              <p class="text-uppercase fw-semibold text-muted text-truncate mb-3">Total Order Amount</p>
              <h4 class="fs-4 mb-0">100,000 IQD</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">Top Ticket Buyers</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive table-card">
            <table class="table table-centered table-wrap align-middle">
              <thead class="text-muted table-light">
                <tr>
                  <th>User</th>
                  <th>Total Orders</th>
                  <th>Total Tickets</th>
                  <th>Total Amount</th>
                  <th>Last Bought Ticket</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>John Doe<br><span class="text-muted">+964 123 456 7890</span></td>
                  <td><span class='badge bg-success-subtle text-success'>10</span></td>
                  <td><span class='badge bg-info-subtle text-info'>50</span></td>
                  <td>15,000 IQD</td>
                  <td>2025-04-15 12:00 PM</td>
                  <td><a href="#" class="btn btn-sm btn-primary">View User</a></td>
                </tr>
                <tr>
                  <td>Jane Smith<br><span class="text-muted">+964 987 654 3210</span></td>
                  <td><span class='badge bg-success-subtle text-success'>7</span></td>
                  <td><span class='badge bg-info-subtle text-info'>35</span></td>
                  <td>10,500 IQD</td>
                  <td>2025-04-14 4:30 PM</td>
                  <td><a href="#" class="btn btn-sm btn-primary">View User</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">Orders</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive table-card">
            <table class="table table-centered table-wrap align-middle">
              <thead class="text-muted table-light">
                <tr>
                  <th>User</th>
                  <th>Total Tickets</th>
                  <th>Grand Total</th>
                  <th>Order Number</th>
                  <th>Payment Method</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>John Doe<br><span class="text-muted">+964 123 456 7890</span></td>
                  <td><span class='badge bg-info-subtle text-info'>5</span></td>
                  <td><span class='badge bg-success-subtle text-success'>7500</span></td>
                  <td>#ORD123456</td>
                  <td><span class="badge bg-primary">Credit Card</span></td>
                  <td><a href="#" class="btn btn-sm btn-primary">View Detail</a></td>
                </tr>
                <tr>
                  <td>Jane Smith<br><span class="text-muted">+964 987 654 3210</span></td>
                  <td><span class='badge bg-info-subtle text-info'>3</span></td>
                  <td><span class='badge bg-success-subtle text-success'>4500</span></td>
                  <td>#ORD654321</td>
                  <td><span class="badge bg-secondary">Cash</span></td>
                  <td><a href="#" class="btn btn-sm btn-primary">View Detail</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
