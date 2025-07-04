@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4 text-center">Orders Placed to Manufacturers</h2>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Manufacturer</th>
                            <th>Product(s)</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ optional($order->product->manufacturer)->name ?? 'N/A' }}</td>
                                <td>{{ $order->product->name ?? 'N/A' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->getStatusBadgeClass() }} text-dark fw-bold">
                                        {{ $order->getStatusText() }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('retailer.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                    @if($order->status === 'cancelled')
                                        <span class="text-danger fw-bold">Order cancelled</span>
                                    @else
                                        <a href="{{ route('retailer.orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('retailer.orders.update-status', $order->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 