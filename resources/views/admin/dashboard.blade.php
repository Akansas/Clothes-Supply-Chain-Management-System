@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
            <p class="text-muted">System-wide overview, recent activity, and quick management links</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-users me-2"></i>Total Users
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $users->count() }}</div>
                            <div class="text-xs text-muted mt-1">All registered users</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-shopping-cart me-2"></i>Total Orders
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $totalPurchaseOrders + $totalRetailerOrders }}</div>
                            <div class="text-xs text-muted mt-1">
                                Purchase: {{ $totalPurchaseOrders }} | Retailer: {{ $totalRetailerOrders }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shopping-cart fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Retailer Orders Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-store me-2"></i>Retailer Orders
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $totalRetailerOrders }}</div>
                            <div class="text-xs text-muted mt-1">
                                @php
                                    $retailerStats = collect($retailerOrdersStats)->filter(function($count) { return $count > 0; });
                                    $statusList = $retailerStats->map(function($count, $status) { return ucfirst($status) . ': ' . $count; })->join(' | ');
                                @endphp
                                {{ $statusList ?: 'No orders' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-store fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Orders Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-industry me-2"></i>Purchase Orders
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $totalPurchaseOrders }}</div>
                            <div class="text-xs text-muted mt-1">
                                @php
                                    $purchaseStats = collect($purchaseOrdersStats)->filter(function($count) { return $count > 0; });
                                    $statusList = $purchaseStats->map(function($count, $status) { return ucfirst($status) . ': ' . $count; })->join(' | ');
                                @endphp
                                {{ $statusList ?: 'No orders' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-industry fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row -->
    <div class="row mb-4">
        <!-- Facility Visits Card -->
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                <i class="fas fa-building me-2"></i>Facility Visits
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalFacilityVisits }}</div>
                            <div class="text-xs text-muted mt-1">
                                Scheduled: {{ $scheduledVisits }} | Total: {{ $totalFacilityVisits }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-building fa-2x text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production Orders Card -->
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fas fa-cogs me-2"></i>Production Orders
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalProductionOrders }}</div>
                            <div class="text-xs text-muted mt-1">
                                Active: {{ $activeProductionOrders }} | Total: {{ $totalProductionOrders }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-cogs fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deliveries Card -->
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                <i class="fas fa-truck me-2"></i>Deliveries
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalDeliveries }}</div>
                            <div class="text-xs text-muted mt-1">
                                Pending: {{ $pendingDeliveries }} | Total: {{ $totalDeliveries }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-dark bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-truck fa-2x text-dark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links to User Management -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Management</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $roleOrder = ['vendor', 'raw_material_supplier', 'manufacturer', 'retailer'];
                            $rolesByName = collect($roles)->keyBy('name');
                        @endphp
                        @foreach($roleOrder as $roleKey)
                            @if($rolesByName->has($roleKey))
                                @php $role = $rolesByName[$roleKey]; @endphp
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('admin.users-by-role', $role->name) }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-user mr-2"></i>{{ ucfirst($role->display_name ?? $role->name) }}
                                        <span class="badge bg-secondary ms-2">{{ $roleCounts[$role->name] ?? 0 }}</span>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Recent Orders</h6>
                </div>
                <div class="card-body">
                    @if($recentOrders->count())
                        <ul class="list-group list-group-flush">
                            @foreach($recentOrders as $order)
                                <li class="list-group-item">
                                    <strong>#{{ $order->order_number ?? $order->id }}</strong>
                                    @php
                                        $orderType = $order->source ?? ($order->retailer_id ? 'retailer' : ($order->manufacturer_id ? 'purchase' : 'unknown'));
                                    @endphp
                                    <span class="badge bg-{{ $orderType == 'purchase' ? 'primary' : ($orderType == 'retailer' ? 'info' : 'secondary') }} ms-2">
                                        {{ ucfirst($orderType) }} Order
                                    </span>
                                    <br>
                                    <span><i class="fas fa-user"></i> {{ $order->user->name ?? 'N/A' }}</span>
                                    @if($order->retailer)
                                        <span class="ms-2"><i class="fas fa-store"></i> Retailer: {{ $order->retailer->name ?? 'N/A' }}</span>
                                    @endif
                                    @if($order->manufacturer)
                                        <span class="ms-2"><i class="fas fa-industry"></i> Manufacturer: {{ $order->manufacturer->name ?? 'N/A' }}</span>
                                    @endif
                                    <br>
                                    <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }} mt-1">{{ ucfirst($order->status) }}</span>
                                    <span class="text-muted float-end">{{ $order->created_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No recent orders.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Recent Facility Visits</h6>
                </div>
                <div class="card-body">
                    @if($recentFacilityVisits->count())
                        <ul class="list-group list-group-flush">
                            @foreach($recentFacilityVisits as $visit)
                                <li class="list-group-item">
                                    @php
                                        $vendorName = $visit->vendor->name ?? $visit->vendor->company_name ?? 'N/A';
                                    @endphp
                                    <strong>Vendor:</strong> {{ $vendorName }}<br>
                                    @if($visit->vendor)
                                        <span class="ms-2"><i class="fas fa-envelope"></i> Email: {{ $visit->vendor->email ?? 'N/A' }}</span>
                                        <span class="ms-2"><i class="fas fa-phone"></i> Phone: {{ $visit->vendor->phone ?? 'N/A' }}</span>
                                    @endif
                                    <br>
                                    <span class="badge bg-{{ $visit->status == 'completed' ? 'success' : ($visit->status == 'scheduled' ? 'info' : 'warning') }} mt-1">{{ ucfirst($visit->status) }}</span>
                                    <span class="text-muted float-end">{{ $visit->created_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No recent facility visits.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Supply Chain Flow Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supply Chain Flow Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center justify-content-center align-items-center">
                        <div class="col-md-3">
                            <div class="supply-chain-step bg-primary text-white p-3 rounded">
                                <i class="fas fa-industry fa-2x mb-2"></i>
                                <h6>Supplier</h6>
                                <small>{{ $supplyChainFlow['suppliers']['active_orders'] ?? 0 }} Active Orders</small>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-3">
                            <div class="supply-chain-step bg-success text-white p-3 rounded">
                                <i class="fas fa-cogs fa-2x mb-2"></i>
                                <h6>Manufacturer</h6>
                                <small>{{ $supplyChainFlow['manufacturers']['active_orders'] ?? 0 }} In Progress</small>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-3">
                            <div class="supply-chain-step bg-danger text-white p-3 rounded">
                                <i class="fas fa-store fa-2x mb-2"></i>
                                <h6>Retailer</h6>
                                <small>{{ $supplyChainFlow['retailers']['active_orders'] ?? 0 }} Orders</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* Enhanced Card Styling */
.card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.border-left-secondary {
    border-left: 4px solid #858796 !important;
}

.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}

.border-left-dark {
    border-left: 4px solid #5a5c69 !important;
}

/* Icon Background Styling */
.bg-opacity-10 {
    opacity: 0.1;
}

.rounded-circle {
    border-radius: 50% !important;
}

.p-3 {
    padding: 1rem !important;
}

/* Text Styling */
.text-primary { color: #4e73df !important; }
.text-success { color: #1cc88a !important; }
.text-warning { color: #f6c23e !important; }
.text-info { color: #36b9cc !important; }
.text-secondary { color: #858796 !important; }
.text-danger { color: #e74a3b !important; }
.text-dark { color: #5a5c69 !important; }

/* Card Body Enhancement */
.card-body {
    padding: 1.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .h2 {
        font-size: 1.5rem;
    }
    
    .h3 {
        font-size: 1.25rem;
    }
}

/* Animation for icons */
.fa-2x {
    transition: transform 0.3s ease;
}

.card:hover .fa-2x {
    transform: scale(1.1);
}
</style> 