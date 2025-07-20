@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Supply Chain Monitoring</h1>
            <p class="text-muted">Real-time monitoring of supply chain operations and flow</p>
        </div>
    </div>

    <!-- Supply Chain Flow Diagram -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supply Chain Flow</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-primary text-white p-3 rounded">
                                <i class="fas fa-industry fa-2x mb-2"></i>
                                <h6>Supplier</h6>
                                <small>{{ $supplyChainData['orders_by_status']->where('status', 'supplier')->first()->count ?? 0 }} Orders</small>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-success text-white p-3 rounded">
                                <i class="fas fa-cogs fa-2x mb-2"></i>
                                <h6>Manufacturer</h6>
                                <small>{{ $supplyChainData['production_orders_by_status']->where('status', 'in_progress')->first()->count ?? 0 }} Active</small>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-info text-white p-3 rounded">
                                <i class="fas fa-warehouse fa-2x mb-2"></i>
                                <h6>Warehouse</h6>
                                <small>Inventory Management</small>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-warning text-white p-3 rounded">
                                <i class="fas fa-truck fa-2x mb-2"></i>
                                <h6>Delivery</h6>
                                <small>{{ $supplyChainData['deliveries_by_status']->where('status', 'pending')->first()->count ?? 0 }} Pending</small>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="col-md-2">
                            <div class="supply-chain-step bg-danger text-white p-3 rounded">
                                <i class="fas fa-user fa-2x mb-2"></i>
                                <h6>Customer</h6>
                                <small>{{ $supplyChainData['orders_by_status']->where('status', 'customer')->first()->count ?? 0 }} Orders</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdowns -->
    <div class="row mb-4">
        <!-- Orders by Status -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Orders by Status</h6>
                </div>
                <div class="card-body">
                    @if($supplyChainData['orders_by_status']->count() > 0)
                        @foreach($supplyChainData['orders_by_status'] as $orderStatus)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ ucfirst($orderStatus->status) }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $orderStatus->status == 'completed' ? 'success' : ($orderStatus->status == 'pending' ? 'warning' : 'info') }}">
                                    {{ $orderStatus->count }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No order data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Deliveries by Status -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deliveries by Status</h6>
                </div>
                <div class="card-body">
                    @if($supplyChainData['deliveries_by_status']->count() > 0)
                        @foreach($supplyChainData['deliveries_by_status'] as $deliveryStatus)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ ucfirst($deliveryStatus->status) }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $deliveryStatus->status == 'completed' ? 'success' : ($deliveryStatus->status == 'pending' ? 'warning' : 'info') }}">
                                    {{ $deliveryStatus->count }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No delivery data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quality and Production -->
    <div class="row mb-4">
        <!-- Production Orders by Status -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Production Orders by Status</h6>
                </div>
                <div class="card-body">
                    @if($supplyChainData['production_orders_by_status']->count() > 0)
                        @foreach($supplyChainData['production_orders_by_status'] as $productionStatus)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ ucfirst(str_replace('_', ' ', $productionStatus->status)) }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $productionStatus->status == 'completed' ? 'success' : ($productionStatus->status == 'in_progress' ? 'info' : 'warning') }}">
                                    {{ $productionStatus->count }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No production order data available</p>
                    @endif
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
                            <a href="{{ route('admin.system-overview') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-chart-line mr-2"></i>System Overview
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-chart-bar mr-2"></i>Analytics
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-shopping-cart mr-2"></i>Order Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.supply-chain-step {
    transition: transform 0.3s ease;
    cursor: pointer;
}

.supply-chain-step:hover {
    transform: scale(1.05);
}

.bg-primary { background-color: #4e73df !important; }
.bg-success { background-color: #1cc88a !important; }
.bg-info { background-color: #36b9cc !important; }
.bg-warning { background-color: #f6c23e !important; }
.bg-danger { background-color: #e74a3b !important; }
</style>
@endsection 