@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <h2 class="fw-bold mb-4 text-center">Welcome, GenZ Admin</h2>
    <p class="lead text-center mb-5">You have full oversight of all users and dashboards in the system.</p>

    <!-- System Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders }}</div>
                            <small class="text-muted">{{ $pendingOrders }} pending</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeProducts }}</div>
                            <small class="text-muted">of {{ $totalProducts }} total</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Deliveries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingDeliveries }}</div>
                            <small class="text-muted">of {{ $totalDeliveries }} total</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Quality Checks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingQualityChecks }}</div>
                            <small class="text-muted">pending of {{ $totalQualityChecks }} total</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supply Chain Flow Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supply Chain Flow Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-primary text-white p-3 rounded mb-2">
                                <i class="fas fa-industry fa-2x mb-2"></i>
                                <h6>Supplier</h6>
                                <small>{{ $supplyChainFlow['suppliers']['count'] }} Users</small>
                                <div class="mt-2">
                                    <span class="badge badge-light">{{ $supplyChainFlow['suppliers']['active_orders'] }} Active Orders</span>
                                </div>
                            </div>
                            <a href="/supplier/dashboard" class="btn btn-sm btn-outline-primary w-100">Access Dashboard</a>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-success text-white p-3 rounded mb-2">
                                <i class="fas fa-cogs fa-2x mb-2"></i>
                                <h6>Manufacturer</h6>
                                <small>{{ $supplyChainFlow['manufacturers']['count'] }} Users</small>
                                <div class="mt-2">
                                    <span class="badge badge-light">{{ $supplyChainFlow['manufacturers']['active_orders'] }} Production Orders</span>
                                </div>
                            </div>
                            <a href="/manufacturer/dashboard" class="btn btn-sm btn-outline-success w-100">Access Dashboard</a>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-secondary text-white p-3 rounded mb-2">
                                <i class="fas fa-store fa-2x mb-2"></i>
                                <h6>Retailer</h6>
                                <small>{{ $supplyChainFlow['retailers']['count'] }} Users</small>
                                <div class="mt-2">
                                    <span class="badge badge-light">{{ $supplyChainFlow['retailers']['active_orders'] }} Orders</span>
                                </div>
                            </div>
                            <a href="/retailer/dashboard" class="btn btn-sm btn-outline-secondary w-100">Access Dashboard</a>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-warning text-white p-3 rounded mb-2">
                                <i class="fas fa-truck fa-2x mb-2"></i>
                                <h6>Delivery</h6>
                                <small>{{ $supplyChainFlow['delivery']['count'] }} Users</small>
                                <div class="mt-2">
                                    <span class="badge badge-light">{{ $supplyChainFlow['delivery']['active_orders'] }} Pending</span>
                                </div>
                            </div>
                            <a href="/delivery/dashboard" class="btn btn-sm btn-outline-warning w-100">Access Dashboard</a>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-danger text-white p-3 rounded mb-2">
                                <i class="fas fa-user fa-2x mb-2"></i>
                                <h6>Customer</h6>
                                <small>{{ $supplyChainFlow['customers']['count'] }} Users</small>
                                <div class="mt-2">
                                    <span class="badge badge-light">{{ $supplyChainFlow['customers']['active_orders'] }} Orders</span>
                                </div>
                            </div>
                            <a href="/customer/dashboard" class="btn btn-sm btn-outline-danger w-100">Access Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Counts by Role -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Management by Role</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($roles as $role)
                        @if(!in_array($role->name, ['inspector', 'warehouse_manager', 'warehouse']))
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-capitalize">{{ $role->display_name ?? ucfirst($role->name) }}</h5>
                                    <p class="card-text">Users: <span class="fw-bold">{{ $userCounts[$role->name] ?? 0 }}</span></p>
                                    <div class="btn-group-vertical w-100" role="group">
                                        <a href="/{{ $role->name }}/dashboard" class="btn btn-outline-primary mb-2">Go to {{ $role->display_name ?? ucfirst($role->name) }} Dashboard</a>
                                        <a href="{{ route('admin.users-by-role', $role->name) }}" class="btn btn-outline-info">Manage {{ $role->display_name ?? ucfirst($role->name) }} Users</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        @foreach($recentOrders as $order)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>Order #{{ $order->order_number }}</strong>
                                <br>
                                <small class="text-muted">{{ $order->user->name ?? 'Unknown User' }} - {{ $order->status }}</small>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                    ${{ number_format($order->total_amount, 2) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No recent orders</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Quality Checks</h6>
                </div>
                <div class="card-body">
                    @if($recentQualityChecks->count() > 0)
                        @foreach($recentQualityChecks as $check)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $check->product->name ?? 'Unknown Product' }}</strong>
                                <br>
                                <small class="text-muted">{{ $check->inspector->name ?? 'Unknown Inspector' }} - {{ $check->status }}</small>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $check->status == 'passed' ? 'success' : ($check->status == 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($check->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No recent quality checks</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Tools -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Tools</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.system-overview') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-chart-line mr-2"></i>System Overview
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
                                <i class="fas fa-comments mr-2"></i>Chat Oversight
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
.text-primary { color: #4e73df !important; }
.text-success { color: #1cc88a !important; }
.text-info { color: #36b9cc !important; }
.text-warning { color: #f6c23e !important; }
.btn-outline-primary { color: #4e73df; border-color: #4e73df; }
.btn-outline-success { color: #1cc88a; border-color: #1cc88a; }
.btn-outline-info { color: #36b9cc; border-color: #36b9cc; }
.btn-outline-warning { color: #f6c23e; border-color: #f6c23e; }
.supply-chain-step {
    transition: transform 0.3s ease;
    cursor: pointer;
}
.supply-chain-step:hover {
    transform: scale(1.02);
}
</style>
@endsection 