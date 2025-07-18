@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Retailer Production Orders</h2>
    <div class="card">
        <div class="card-body">
            @if($orders->count() > 0)
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Manufacturer</th>
                            <th>Retailer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Order Placed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ optional($order->manufacturer)->name ?? ($order->manufacturer_id ? (\App\Models\Manufacturer::find($order->manufacturer_id)->name ?? '-') : '-') }}</td>
                            <td>{{ $order->retailer->name ?? 'N/A' }}</td>
                            <td>{{ $order->product->name ?? 'N/A' }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>{{ $order->due_date ? \Illuminate\Support\Carbon::parse($order->due_date)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('manufacturer.production-orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                                @if($order->status === 'pending')
                                    <form method="POST" action="{{ route('manufacturer.retailer-orders.updateStatus', $order->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('manufacturer.retailer-orders.updateStatus', $order->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @elseif($order->status === 'approved')
                                    <form method="POST" action="{{ route('manufacturer.retailer-orders.updateStatus', $order->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" class="btn btn-sm btn-info">Mark as Delivered</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $orders->links() }}
            @else
                <p class="text-muted">No production orders from retailers found.</p>
            @endif
        </div>
    </div>
    <a href="{{ route('manufacturer.dashboard') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
@endsection
