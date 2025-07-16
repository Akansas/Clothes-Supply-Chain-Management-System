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

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="status" class="col-form-label">Filter by Status:</label>
                    </div>
                    <div class="col-auto">
                        <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </form>
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
                            <td>GenZ FashionZ</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge order-status-badge {{ $order->getStatusBadgeClass() }}">{{ $order->getStatusText() }}</span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('supplier.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                                @if($order->status === 'pending')
                                    <form action="{{ route('supplier.orders.updateStatus', $order) }}" method="POST" class="d-inline-block ms-1">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('supplier.orders.updateStatus', $order) }}" method="POST" class="d-inline-block ms-1">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @elseif($order->status === 'approved')
                                    <form action="{{ route('supplier.orders.updateStatus', $order) }}" method="POST" class="d-inline-block ms-1">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" class="btn btn-sm btn-primary">Mark as Delivered</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No purchase orders found.<br>
                                @if(!request('status'))
                                    <small>If you expect to see orders here, please ensure your supplier profile is correctly linked to your user account and that manufacturers are placing orders for your raw materials.</small>
                                @endif
                            </td>
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

@push('styles')
<style>
    .order-status-badge {
        font-weight: bold;
        font-size: 1rem;
        padding: 0.4em 0.8em;
        border: 1.5px solid #222;
        letter-spacing: 0.5px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        opacity: 0.95;
        color: #111 !important;
        background: #fff !important;
    }
</style>
@endpush

@stack('styles') 