@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Welcome, {{ $user->name }} (Customer)</h2>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $totalOrders }}</h4>
                    <p class="card-text">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $pendingOrders }}</h4>
                    <p class="card-text">Pending Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $completedOrders }}</h4>
                    <p class="card-text">Completed Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">${{ number_format($totalSpent, 2) }}</h4>
                    <p class="card-text">Total Spent</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <a href="{{ route('customer.products.browse') }}" class="btn btn-outline-primary w-100">Browse Products</a>
            </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('customer.cart') }}" class="btn btn-outline-success w-100">Shopping Cart</a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('customer.orders') }}" class="btn btn-outline-warning w-100">My Orders</a>
            </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('customer.profile') }}" class="btn btn-outline-info w-100">My Profile</a>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @if($order->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($order->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No orders yet. <a href="{{ route('customer.products.browse') }}">Start shopping!</a></p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Recent Deliveries -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Deliveries</h5>
                </div>
                <div class="card-body">
                    @if($recentDeliveries->count() > 0)
                        @foreach($recentDeliveries as $delivery)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">Order #{{ $delivery->order->order_number ?? 'N/A' }}</h6>
                                <small class="text-muted">{{ $delivery->created_at->format('M d, Y') }}</small>
                            </div>
                            <span class="badge bg-{{ $delivery->status == 'delivered' ? 'success' : 'warning' }}">
                                {{ ucfirst($delivery->status) }}
                            </span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No recent deliveries</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light">Order Tracking</div>
                <div class="card-body text-muted">
                    <p>Track your orders in real-time and get delivery updates.</p>
                    <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary">View All Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light">Customer Support</div>
                <div class="card-body text-muted">
                    <p>Need help? Contact our support team or chat with vendors.</p>
                    <a href="#" class="btn btn-outline-primary">Get Support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 