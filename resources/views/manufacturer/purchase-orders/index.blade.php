@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">My Purchase Orders</h2>
            <p class="text-muted">View, edit, or cancel your raw material orders to suppliers.</p>
        </div>
        <a href="{{ route('manufacturer.materials.browse') }}" class="btn btn-outline-primary">Order Raw Materials</a>
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
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Supplier</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        @php
                            $badgeTextColor = $order->getStatusBadgeClass() === 'badge-warning' ? '#212529' : '#fff';
                            $badgeStyle = 'font-size: 1.05em; font-weight: bold; padding: 0.6em 1.2em; box-shadow: 0 1px 4px rgba(0,0,0,0.08); color: ' . $badgeTextColor . ';';
                        @endphp
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                @if($order->supplier)
                                    {{ $order->supplier->company_name }}
                                    @if($order->supplier->user)
                                        ({{ $order->supplier->user->name }})
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $order->getStatusBadgeClass() }}" style="font-size: 1em; font-weight: 600; padding: 0.4em 1em; border-radius: 1em; color: #111; background: #fff;">
                                    {{ $order->getStatusText() ?: strtoupper($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-end">
                                @if($order->status === 'pending')
                                    <a href="{{ route('manufacturer.purchase-orders.edit', $order) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('manufacturer.purchase-orders.cancel', $order) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
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