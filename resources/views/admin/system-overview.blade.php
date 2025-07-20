@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">System Overview</h1>
            <p class="text-muted">Complete system statistics and performance metrics</p>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_products'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Deliveries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_deliveries'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row mb-4">
        <!-- Removed Quality Checks card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Facility Visits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_facility_visits'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Production Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_production_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-industry fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-light shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-light text-uppercase mb-1">System Status</div>
                            <div class="h5 mb-0 font-weight-bold text-success">Online</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Health</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Database Status</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 95%">95%</div>
                            </div>
                            <small class="text-muted">Database connections: Active</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Server Performance</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 78%">78%</div>
                            </div>
                            <small class="text-muted">CPU Usage: Normal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>System Reports</h5>
                    <div>
                        <a href="{{ route('admin.reports.download', ['type' => 'pdf']) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-file-pdf me-1"></i>Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- User Activity Summary -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success"><i class="fas fa-users me-2"></i>User Activity Summary</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Total Users</small>
                                            <div class="fw-bold">{{ $stats['total_users'] }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Active Users</small>
                                            <div class="fw-bold text-success">{{ $stats['active_users'] ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <small class="text-muted">New Users (This Month)</small>
                                            <div class="fw-bold text-info">{{ $stats['new_users_this_month'] ?? 0 }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Online Users</small>
                                            <div class="fw-bold text-primary">{{ $stats['online_users'] ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Performance -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary"><i class="fas fa-server me-2"></i>System Performance</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Total Orders</small>
                                            <div class="fw-bold">{{ $stats['total_orders'] }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Total Products</small>
                                            <div class="fw-bold text-info">{{ $stats['total_products'] }}</div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <small class="text-muted">Total Deliveries</small>
                                            <div class="fw-bold text-warning">{{ $stats['total_deliveries'] }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Quality Checks</small>
                                            <div class="fw-bold text-danger">{{ $stats['total_quality_checks'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Metrics -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-3"><i class="fas fa-chart-line me-2"></i>System Metrics</h6>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle p-2 me-2">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted">System Status</small>
                                            <div class="fw-bold text-success">Online</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle p-2 me-2">
                                            <i class="fas fa-database text-white"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted">Database</small>
                                            <div class="fw-bold text-info">95%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle p-2 me-2">
                                            <i class="fas fa-microchip text-white"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted">CPU Usage</small>
                                            <div class="fw-bold text-warning">78%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle p-2 me-2">
                                            <i class="fas fa-memory text-white"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted">Memory</small>
                                            <div class="fw-bold text-primary">65%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent System Events -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-3"><i class="fas fa-history me-2"></i>Recent System Events</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Event</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ now()->format('M d, H:i') }}</td>
                                            <td>System backup completed</td>
                                            <td><span class="badge bg-success">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subMinutes(30)->format('M d, H:i') }}</td>
                                            <td>Database optimization</td>
                                            <td><span class="badge bg-info">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subHours(2)->format('M d, H:i') }}</td>
                                            <td>New user registration</td>
                                            <td><span class="badge bg-primary">Processed</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subHours(4)->format('M d, H:i') }}</td>
                                            <td>Order processing batch</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-tachometer-alt mr-2"></i>Main Dashboard
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.supply-chain-monitoring') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-network-wired mr-2"></i>Supply Chain Monitor
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('analytics.dashboard') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-chart-bar mr-2"></i>Analytics
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('chat.dashboard') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-comments mr-2"></i>Chat System
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-secondary { border-left: 0.25rem solid #858796 !important; }
.border-left-dark { border-left: 0.25rem solid #5a5c69 !important; }
.border-left-light { border-left: 0.25rem solid #f8f9fc !important; }
.text-primary { color: #4e73df !important; }
.text-success { color: #1cc88a !important; }
.text-info { color: #36b9cc !important; }
.text-warning { color: #f6c23e !important; }
.text-danger { color: #e74a3b !important; }
.text-secondary { color: #858796 !important; }
.text-dark { color: #5a5c69 !important; }
.text-light { color: #f8f9fc !important; }
</style>
@endsection 