@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Order Details</h2>
        <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary">Back to Orders</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Status:</strong> 
                                @if($order->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @elseif($order->status == 'shipped')
                                    <span class="badge bg-primary">Shipped</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                            <p><strong>Payment Status:</strong> 
                                @if($order->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->payment_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                @endif
                            </p>
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    @foreach($order->orderItems as $item)
                    <div class="row align-items-center mb-3 pb-3 border-bottom">
                        <div class="col-md-2">
                            <div class="bg-light text-center py-2" style="height: 80px;">
                                @if($item->product && $item->product->image_url)
                                    <img src="{{ $item->product->image_url }}" class="img-fluid" style="max-height: 70px;" alt="{{ $item->product->name }}">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">{{ $item->product->name ?? 'Unknown Product' }}</h6>
                            <small class="text-muted">
                                @if($item->product && $item->product->vendor)
                                    by {{ $item->product->vendor->company_name }}
                                @endif
                            </small>
                        </div>
                        <div class="col-md-2">
                            <span class="text-muted">Qty: {{ $item->quantity }}</span>
                        </div>
                        <div class="col-md-2">
                            <span class="fw-bold">${{ number_format($item->total_price, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Shipping Address:</strong></p>
                    <p class="text-muted">{{ $order->shipping_address }}</p>
                    
                    <p><strong>Billing Address:</strong></p>
                    <p class="text-muted">{{ $order->billing_address }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Delivery Status -->
            @if($order->deliveries->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Status</h5>
                </div>
                <div class="card-body">
                    @foreach($order->deliveries as $delivery)
                    <div class="mb-3">
                        <p><strong>Status:</strong> 
                            @if($delivery->status == 'delivered')
                                <span class="badge bg-success">Delivered</span>
                            @elseif($delivery->status == 'in_transit')
                                <span class="badge bg-primary">In Transit</span>
                            @elseif($delivery->status == 'out_for_delivery')
                                <span class="badge bg-info">Out for Delivery</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($delivery->status) }}</span>
                            @endif
                        </p>
                        @if($delivery->driver)
                            <p><strong>Driver:</strong> {{ $delivery->driver->name }}</p>
                        @endif
                        @if($delivery->tracking_number)
                            <p><strong>Tracking:</strong> {{ $delivery->tracking_number }}</p>
                        @endif
                        @if($delivery->estimated_delivery)
                            <p><strong>Estimated Delivery:</strong> {{ $delivery->estimated_delivery->format('M d, Y') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Order Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.orders.track', $order->id) }}" class="btn btn-outline-info">Track Order</a>
                        @if($order->status == 'pending')
                            <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</button>
                            </form>
                        @endif
                        @if($order->status == 'completed')
                            <button class="btn btn-outline-success">Download Invoice</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 