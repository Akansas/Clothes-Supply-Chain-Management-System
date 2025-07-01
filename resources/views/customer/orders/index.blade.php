@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Orders</h2>
        <a href="{{ route('customer.products.browse') }}" class="btn btn-outline-primary">Browse Products</a>
    </div>

    @if($orders->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @foreach($order->orderItems->take(2) as $item)
                                        <div>{{ $item->product->name ?? 'Unknown Product' }} ({{ $item->quantity }})</div>
                                    @endforeach
                                    @if($order->orderItems->count() > 2)
                                        <small class="text-muted">+{{ $order->orderItems->count() - 2 }} more items</small>
                                    @endif
                                </td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
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
                                </td>
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($order->payment_status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                                        <a href="{{ route('customer.orders.track', $order->id) }}" class="btn btn-outline-info btn-sm">Track</a>
                                        @if($order->status == 'pending')
                                            <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <h4 class="text-muted">No orders yet</h4>
            <p class="text-muted">Start shopping to see your orders here!</p>
            <a href="{{ route('customer.products.browse') }}" class="btn btn-primary">Browse Products</a>
        </div>
    @endif
</div>
@endsection 