@extends('layouts.master')

@section('title')
  @lang('translation.dashboards')
@endsection

@section('css')
  <link href="{{ URL::asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ URL::asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@vite(['resources/js/app.js'])
<style>
   :root {
            --primary: #4361ee;
            --secondary: #6c757d;
            --success: #3ddc97;
            --info: #4cc9f0;
            --warning: #f8961e;
            --danger: #e5383b;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-bg: #1e2a45;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #4a5568;
            overflow-x: hidden;
        }
        
        /* Sidebar styling */
      
        
        
        
        /* Main content area */
        .main-content {
       
            transition: var(--transition);
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-weight: 600;
            margin-bottom: 0;
            color: var(--primary);
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: none;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .stats-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .stats-card .icon.primary {
            background: rgba(67, 97, 238, 0.15);
            color: var(--primary);
        }
        
        .stats-card .icon.warning {
            background: rgba(248, 150, 30, 0.15);
            color: var(--warning);
        }
        
        .stats-card .icon.info {
            background: rgba(76, 201, 240, 0.15);
            color: var(--info);
        }
        
        .stats-card .value {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .stats-card .label {
            color: #718096;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            border: none;
        }
        
        .content-card .card-header {
            background: transparent;
            border: none;
            padding: 0 0 20px 0;
            margin: 0;
        }
        
        .content-card .card-title {
            font-weight: 600;
            font-size: 18px;
            color: #2d3748;
            margin: 0;
        }
        
        /* Tables */
        .custom-table {
            border-collapse: separate;
            border-spacing: 0 8px;
            width: 100%;
        }
        
        .custom-table th {
            background: #f7fafc;
            color: #718096;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            border: none;
        }
        
        .custom-table td {
            background: white;
            padding: 15px;
            border: none;
            vertical-align: middle;
            border-top: 1px solid #edf2f7;
        }
        
        .custom-table tr:first-child td {
            border-top: none;
        }
        
        .custom-table tr {
            transition: var(--transition);
            border-radius: 8px;
        }
        
        .custom-table tr:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        
        .custom-table .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar .logo-text,
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
</style>
@section('content')
 <div class="wrapper d-flex">
        <!-- Sidebar -->
      

        <!-- Main Content -->
        <div class="main-content">
         

            <!-- Stats Row -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="icon primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="value">1,248</div>
                        <div class="label">Total Users</div>
                        <div class="mt-3">
                            <span class="text-success"><i class="bi bi-arrow-up"></i> 12.4%</span> <span class="text-muted">from last month</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="icon warning">
                            <i class="bi bi-bus-front"></i>
                        </div>
                        <div class="value">48</div>
                        <div class="label">Total Bus</div>
                        <div class="mt-3">
                            <span class="text-success"><i class="bi bi-arrow-up"></i> 3.2%</span> <span class="text-muted">from last month</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="icon info">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="value">24</div>
                        <div class="label">Total Cabins</div>
                        <div class="mt-3">
                            <span class="text-danger"><i class="bi bi-arrow-down"></i> 1.8%</span> <span class="text-muted">from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Data Row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="content-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Ticket Sales Analytics</h5>
                            <div>
                                <select class="form-select form-select-sm">
                                    <option>Last 7 days</option>
                                    <option selected>Last 30 days</option>
                                    <option>Last 90 days</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="content-card">
                        <div class="card-header">
                            <h5 class="card-title">Revenue Distribution</h5>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables Row -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="content-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Top Ticket Buyers</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table custom-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Total Orders</th>
                                        <th>Total Tickets</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <div class="bg-primary rounded-circle p-2 text-white">JD</div>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">John Doe</div>
                                                    <div class="text-muted small">+964 123 456 7890</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success">10</span></td>
                                        <td><span class="badge bg-info-subtle text-info">50</span></td>
                                        <td class="fw-semibold">15,000 IQD</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <div class="bg-warning rounded-circle p-2 text-white">JS</div>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Jane Smith</div>
                                                    <div class="text-muted small">+964 987 654 3210</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success">7</span></td>
                                        <td><span class="badge bg-info-subtle text-info">35</span></td>
                                        <td class="fw-semibold">10,500 IQD</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <div class="bg-info rounded-circle p-2 text-white">MR</div>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Mohammed Rahman</div>
                                                    <div class="text-muted small">+964 555 123 4567</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success">5</span></td>
                                        <td><span class="badge bg-info-subtle text-info">25</span></td>
                                        <td class="fw-semibold">7,500 IQD</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="content-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Recent Orders</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table custom-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">#ORD123456</td>
                                        <td>
                                            <div>John Doe</div>
                                            <div class="text-muted small">5 tickets</div>
                                        </td>
                                        <td class="fw-semibold">7,500 IQD</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">#ORD654321</td>
                                        <td>
                                            <div>Jane Smith</div>
                                            <div class="text-muted small">3 tickets</div>
                                        </td>
                                        <td class="fw-semibold">4,500 IQD</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">#ORD789012</td>
                                        <td>
                                            <div>Ali Hassan</div>
                                            <div class="text-muted small">2 tickets</div>
                                        </td>
                                        <td class="fw-semibold">3,000 IQD</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['1 Jan', '5 Jan', '10 Jan', '15 Jan', '20 Jan', '25 Jan', '30 Jan'],
                    datasets: [{
                        label: 'Ticket Sales',
                        data: [1200, 1900, 1500, 2200, 1800, 2500, 3000],
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#4361ee',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Bus Tickets', 'VIP Cabins', 'Other Services'],
                    datasets: [{
                        data: [65, 25, 10],
                        backgroundColor: [
                            '#4361ee',
                            '#f8961e',
                            '#4cc9f0'
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection

