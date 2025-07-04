@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Order Details</h2>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Order #</dt>
                <dd class="col-sm-8">{{ $order->order_number }}</dd>

                <dt class="col-sm-4">Product</dt>
                <dd class="col-sm-8">{{ $order->product->name ?? 'N/A' }}</dd>

                <dt class="col-sm-4">Quantity</dt>
                <dd class="col-sm-8">{{ $order->quantity }}</dd>

                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">
                    <span class="badge bg-{{ $order->getStatusBadgeClass() }} text-dark fw-bold">{{ $order->getStatusText() }}</span>
                </dd>

                <dt class="col-sm-4">Due Date</dt>
                <dd class="col-sm-8">{{ $order->due_date ? \Carbon\Carbon::parse($order->due_date)->format('M d, Y') : 'N/A' }}</dd>

                <dt class="col-sm-4">Total Cost</dt>
                <dd class="col-sm-8">${{ number_format($order->total_amount, 2) }}</dd>

                <dt class="col-sm-4">Shipping Address</dt>
                <dd class="col-sm-8">{{ $order->shipping_address }}</dd>

                <dt class="col-sm-4">Shipping City</dt>
                <dd class="col-sm-8">{{ $order->shipping_city }}</dd>

                <dt class="col-sm-4">Shipping State</dt>
                <dd class="col-sm-8">{{ $order->shipping_state }}</dd>

                <dt class="col-sm-4">Shipping Zip</dt>
                <dd class="col-sm-8">{{ $order->shipping_zip }}</dd>

                <dt class="col-sm-4">Shipping Country</dt>
                <dd class="col-sm-8">{{ $order->shipping_country }}</dd>

                <dt class="col-sm-4">Notes</dt>
                <dd class="col-sm-8">{{ $order->notes ?? '-' }}</dd>
            </dl>
            <div class="d-grid">
                <a href="{{ route('retailer.orders') }}" class="btn btn-secondary">Back to Orders</a>
            </div>
        </div>
    </div>
</div>
@endsection 