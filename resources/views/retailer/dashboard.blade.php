@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Retail Store Dashboard</h4>
                    <p class="card-category">Welcome back, {{ auth()->user()->name }}</p>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <!-- First row: 4 cards -->
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total products in store</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['products_count'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Low Stock Products</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['low_stock_products'] }}</div>
                                            @if(isset($lowStockItems) && $lowStockItems->count())
                                                <ul class="mt-2 mb-0 ps-3" style="font-size: 0.95em;">
                                                    @foreach($lowStockItems as $item)
                                                        <li>{{ $item->product->name ?? 'N/A' }} ({{ $item->quantity }})</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-muted mt-2" style="font-size: 0.95em;">All products well stocked</div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-list-ol fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_orders'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <!-- Second row: 4 cards -->
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Delivered Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['delivered_orders'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Approved Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved_orders'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-thumbs-up fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cancelled Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['cancelled_orders'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-secondary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Rejected Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected_orders'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Quick Actions</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('retailer.products.browse') }}" class="btn btn-outline-primary btn-block">
                                                <i class="fa fa-search"></i> Browse and order manufacturer products
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('retailer.production-orders.index') }}" class="btn btn-primary btn-block">
                                                <i class="fa fa-shopping-cart"></i> Manage Orders
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('retailer.inventory') }}" class="btn btn-info btn-block">
                                                <i class="fa fa-box"></i> Inventory Management
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('retailer.analytics') }}" class="btn btn-success btn-block">
                                                <i class="fa fa-chart-bar"></i> View Analytics
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Recent Orders</h4>
                                    <p class="card-category">Latest orders</p>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentOrders as $order)
                                                    <tr>
                                                        <td>{{ $order->order_number }}</td>
                                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $order->getStatusBadgeClass() }}" style="color: #000 !important;">
                                                                {{ $order->getStatusText() }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('retailer.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No recent orders</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <a href="{{ route('retailer.orders') }}" class="text-decoration-none">
                                            <i class="fa fa-eye"></i> View All Orders
                                        </a>
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
                                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Daily Reports</h5>
                                    <div>
                                        <a href="{{ route('retailer.reports.download', ['type' => 'pdf']) }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-file-pdf me-1"></i>Download PDF
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Sales Analytics -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-left-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success"><i class="fas fa-chart-line me-2"></i>Sales Analytics</h6>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Total Orders</small>
                                                            <div class="fw-bold">{{ $stats['total_orders'] ?? 0 }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Monthly Orders</small>
                                                            <div class="fw-bold text-info">{{ $stats['monthly_orders'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">Total Cost</small>
                                                            <div class="fw-bold text-dark">${{ number_format($stats['total_cost'] ?? 0, 2) }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Delivered Orders</small>
                                                            <div class="fw-bold text-success">{{ $stats['delivered_orders'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Inventory Summary -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-left-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary"><i class="fas fa-boxes me-2"></i>Inventory Summary</h6>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Total Products</small>
                                                            <div class="fw-bold">{{ $stats['products_count'] ?? 0 }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Low Stock Items</small>
                                                            <div class="fw-bold text-warning">{{ $stats['low_stock_products'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">Pending Orders</small>
                                                            <div class="fw-bold text-warning">{{ $stats['pending_orders'] ?? 0 }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Approved Orders</small>
                                                            <div class="fw-bold text-primary">{{ $stats['approved_orders'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Status Summary -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="text-muted mb-3"><i class="fas fa-list-check me-2"></i>Order Status Summary</h6>
                                            <div class="row">
                                                <div class="col-md-3 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success rounded-circle p-2 me-2">
                                                            <i class="fas fa-check text-white"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Delivered</small>
                                                            <div class="fw-bold">{{ $stats['delivered_orders'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-warning rounded-circle p-2 me-2">
                                                            <i class="fas fa-clock text-white"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Pending</small>
                                                            <div class="fw-bold">{{ $stats['pending_orders'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-danger rounded-circle p-2 me-2">
                                                            <i class="fas fa-times text-white"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Cancelled</small>
                                                            <div class="fw-bold">{{ $stats['cancelled_orders'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-secondary rounded-circle p-2 me-2">
                                                            <i class="fas fa-ban text-white"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Rejected</small>
                                                            <div class="fw-bold">{{ $stats['rejected_orders'] ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul>
    

<!-- Alpine Dropdown Chat Button with Partners -->
<div x-data="{ open: false }" class="relative inline-block text-left">
    <button @click="open = !open"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        Chat with Manufacturers
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" @click.away="open = false"
         x-transition
         class="absolute z-50 mt-2 w-72 bg-white shadow-lg rounded-lg border border-gray-200">

        <!-- Suppliers -->
        <div class="px-4 py-2 border-b font-semibold text-gray-700">Manufacturers</div>
        @forelse ($manufacturers as $manufacturer)
            <a href="{{ route('retailer.chat.index', ['partner' => $manufacturer->id]) }}"
               class="block px-4 py-2 text-sm text-gray-800 hover:bg-blue-100">
                {{ $manufacturer->name }}
            </a>
        @empty
            <div class="px-4 py-2 text-sm text-gray-400">No manufacturers</div>
        @endforelse


                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <!-- This button is removed as per the edit hint -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Remove all ML-related analytics sections and cards. Only keep simple stats, recent orders, and low stock items. -->
</div>
@endsection 