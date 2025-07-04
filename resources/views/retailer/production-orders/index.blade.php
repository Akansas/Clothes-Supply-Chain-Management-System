@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <!-- Debug Info -->
    <div class="alert alert-info mb-4">
        <strong>Debug Info:</strong><br>
        Current User ID: {{ auth()->id() }}<br>
        Orders for this user: {{ $productionOrders->total() }}
    </div>
    @if($productionOrders->total() === 0 && isset($allProductionOrders))
        <div class="alert alert-warning">
            <strong>No orders found for this user.</strong><br>
            <span>Showing all orders for debugging:</span>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order #</th>
                                <th>Retailer ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allProductionOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->retailer_id }}</td>
                                    <td>{{ optional($order->product)->name }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    <!-- End Debug Info -->
    <h2 class="fw-bold mb-4 text-center">Orders to Manufacturers</h2>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Product</th>
                            <th>Manufacturer</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productionOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->product->name ?? 'N/A' }}</td>
                                <td>{{ $order->product->manufacturer->name ?? 'N/A' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>
                                    <span class="badge bg-secondary text-dark fw-bold">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td>{{ $order->due_date ? \Carbon\Carbon::parse($order->due_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>
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
                {{ $productionOrders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 