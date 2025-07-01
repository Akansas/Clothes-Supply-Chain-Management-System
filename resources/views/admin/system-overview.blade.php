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
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Quality Checks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_quality_checks'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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