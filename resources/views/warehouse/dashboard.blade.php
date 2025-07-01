@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Welcome, {{ $warehouse->name ?? auth()->user()->name }}</h2>
            <p class="lead text-muted">Manage inventory, fulfillments, and warehouse operations</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_products'] }}</h4>
                            <small>Total Products</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['low_stock_products'] }}</h4>
                            <small>Low Stock</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['out_of_stock'] }}</h4>
                            <small>Out of Stock</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending_fulfillments'] }}</h4>
                            <small>Pending Orders</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shipping-fast fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Inventory Movements -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Recent Inventory Movements</h5>
                </div>
                <div class="card-body">
                    @if($recentMovements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMovements as $movement)
                                    <tr>
                                        <td>{{ $movement->product->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $movement->quantity > 20 ? 'success' : ($movement->quantity > 0 ? 'warning' : 'danger') }}">
                                                {{ $movement->quantity }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent inventory movements</p>
                    @endif
                    <a href="{{ route('warehouse.inventory') }}" class="btn btn-outline-primary btn-sm">View All Inventory</a>
                </div>
            </div>
        </div>

        <!-- Pending Fulfillments -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Pending Fulfillments</h5>
                </div>
                <div class="card-body">
                    @if($pendingFulfillments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingFulfillments as $fulfillment)
                                    <tr>
                                        <td>#{{ $fulfillment->id }}</td>
                                        <td>{{ $fulfillment->user->name ?? 'N/A' }}</td>
                                        <td>{{ $fulfillment->orderItems->count() }}</td>
                                        <td>
                                            <span class="badge bg-warning">{{ ucfirst($fulfillment->status) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No pending fulfillments</p>
                    @endif
                    <a href="{{ route('warehouse.fulfillments') }}" class="btn btn-outline-primary btn-sm">View All Fulfillments</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Low Stock Alerts</h5>
                </div>
                <div class="card-body">
                    @if($lowStockItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Reorder Level</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->quantity > 0 ? 'warning' : 'danger' }}">
                                                {{ $item->quantity }}
                                            </span>
                                        </td>
                                        <td>{{ $item->reorder_level }}</td>
                                        <td>
                                            @if($item->quantity == 0)
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @else
                                                <span class="badge bg-warning">Low Stock</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('warehouse.inventory') }}" class="btn btn-sm btn-outline-primary">Update</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No low stock alerts</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap gap-2">
            <a href="{{ route('warehouse.inventory') }}" class="btn btn-outline-primary">Manage Inventory</a>
            <a href="{{ route('warehouse.fulfillments') }}" class="btn btn-outline-success">Fulfill Orders</a>
            <a href="{{ route('warehouse.assign-delivery') }}" class="btn btn-outline-warning">Assign Delivery</a>
            <a href="{{ route('warehouse.analytics') }}" class="btn btn-outline-info">Generate Reports</a>
            <a href="{{ route('warehouse.chat') }}" class="btn btn-outline-secondary">Chat</a>
        </div>
    </div>
</div>
@endsection 