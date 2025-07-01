@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Track Order</h2>
        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-primary">Back to Order</a>
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
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <div class="col-md-6">
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
                            <p><strong>Payment Status:</strong> 
                                @if($order->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->payment_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Timeline</h5>
                </div>
                <div class="card-body">
                    @if($order->deliveries->count() > 0)
                        @foreach($order->deliveries as $delivery)
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>Order Placed</h6>
                                    <p class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                    <p>Your order has been placed and is being processed.</p>
                                </div>
                            </div>
                            
                            @if($order->status == 'processing' || $order->status == 'shipped' || $order->status == 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6>Order Processing</h6>
                                    <p class="text-muted">{{ $order->updated_at->format('M d, Y H:i') }}</p>
                                    <p>Your order is being prepared for shipment.</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($delivery->status == 'in_transit' || $delivery->status == 'out_for_delivery' || $delivery->status == 'delivered')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6>In Transit</h6>
                                    <p class="text-muted">{{ $delivery->created_at->format('M d, Y H:i') }}</p>
                                    <p>Your order is on its way to you.</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($delivery->status == 'out_for_delivery')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6>Out for Delivery</h6>
                                    <p class="text-muted">{{ $delivery->updated_at->format('M d, Y H:i') }}</p>
                                    <p>Your order is out for delivery today.</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($delivery->status == 'delivered')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Delivered</h6>
                                    <p class="text-muted">{{ $delivery->updated_at->format('M d, Y H:i') }}</p>
                                    <p>Your order has been delivered successfully!</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No delivery information available yet.</p>
                            <p class="text-muted">Your order is being processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Delivery Details -->
            @if($order->deliveries->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Details</h5>
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
                            @if($delivery->driver->phone)
                                <p><strong>Driver Phone:</strong> {{ $delivery->driver->phone }}</p>
                            @endif
                        @endif
                        
                        @if($delivery->tracking_number)
                            <p><strong>Tracking Number:</strong> {{ $delivery->tracking_number }}</p>
                        @endif
                        
                        @if($delivery->estimated_delivery)
                            <p><strong>Estimated Delivery:</strong> {{ $delivery->estimated_delivery->format('M d, Y') }}</p>
                        @endif
                        
                        @if($delivery->actual_delivery_date)
                            <p><strong>Actual Delivery:</strong> {{ $delivery->actual_delivery_date->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Shipping Address -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 3px #dee2e6;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -29px;
    top: 12px;
    width: 2px;
    height: calc(100% + 18px);
    background-color: #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-content p {
    margin-bottom: 5px;
}
</style>
@endsection 