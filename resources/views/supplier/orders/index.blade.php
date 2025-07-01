@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Purchase Orders</h2>
            <p class="text-muted">View and manage incoming orders from manufacturers.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Manufacturer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->manufacturer->name ?? 'N/A' }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $order->getStatusBadgeClass() }}">{{ $order->getStatusText() }}</span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('supplier.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No purchase orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 