@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Welcome, {{ $vendor->name ?? auth()->user()->name }}</h2>
            <p class="lead text-muted">Manage your products, applications, and facility visits</p>
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
                            <i class="fas fa-box fa-2x"></i>
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
                            <h4 class="mb-0">{{ $stats['active_products'] }}</h4>
                            <small>Active Products</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="mb-0">{{ $stats['pending_applications'] }}</h4>
                            <small>Pending Applications</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['upcoming_visits'] }}</h4>
                            <small>Upcoming Visits</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Applications -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Recent Applications</h5>
                </div>
                <div class="card-body">
                    @if($recentApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications as $application)
                                    <tr>
                                        <td>{{ $application->product->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent applications</p>
                    @endif
                    <a href="{{ route('vendor.applications') }}" class="btn btn-outline-primary btn-sm">View All Applications</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Facility Visits -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Upcoming Facility Visits</h5>
                </div>
                <div class="card-body">
                    @if($upcomingVisits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Inspector</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingVisits as $visit)
                                    <tr>
                                        <td>{{ $visit->inspector->name ?? 'N/A' }}</td>
                                        <td>{{ $visit->scheduled_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $visit->status === 'scheduled' ? 'info' : ($visit->status === 'completed' ? 'success' : 'warning') }}">
                                                {{ ucfirst($visit->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No upcoming facility visits</p>
                    @endif
                    <a href="{{ route('vendor.facility-visits') }}" class="btn btn-outline-primary btn-sm">View All Visits</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Products</h5>
                </div>
                <div class="card-body">
                    @if($recentProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category }}</td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('vendor.products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No products found</p>
                    @endif
                    <a href="{{ route('vendor.products') }}" class="btn btn-outline-primary">View All Products</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>Add New Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('vendor.applications.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-file-alt me-2"></i>New Application
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('vendor.designs') }}" class="btn btn-info w-100">
                                <i class="fas fa-palette me-2"></i>Manage Designs
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('vendor.analytics') }}" class="btn btn-warning w-100">
                                <i class="fas fa-chart-bar me-2"></i>Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap gap-2">
            <a href="{{ route('vendor.product-offers') }}" class="btn btn-outline-primary">View Product Offers</a>
            <a href="{{ route('vendor.orders.create') }}" class="btn btn-outline-success">Place Bulk Order</a>
            <a href="{{ route('vendor.orders.track') }}" class="btn btn-outline-info">Track Orders</a>
            <a href="{{ route('vendor.products.list-for-retailers') }}" class="btn btn-outline-warning">List Product for Retailers</a>
            <a href="{{ route('vendor.chat') }}" class="btn btn-outline-secondary">Chat</a>
        </div>
    </div>
</div>
@endsection 