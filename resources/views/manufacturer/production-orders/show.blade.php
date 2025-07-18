@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Production Order #{{ $order->order_number }}</h2>
    <div class="card mb-4">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Retailer</dt>
                <dd class="col-sm-9">{{ $order->retailer->name ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Product</dt>
                <dd class="col-sm-9">{{ $order->product->name ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Quantity</dt>
                <dd class="col-sm-9">{{ $order->quantity }}</dd>
                <dt class="col-sm-3">Unit Price</dt>
                <dd class="col-sm-9">{{ $order->product && $order->product->price ? '$' . number_format($order->product->price, 2) : '-' }}</dd>
                <dt class="col-sm-3">Total Revenue</dt>
                <dd class="col-sm-9">{{ ($order->product && $order->quantity) ? '$' . number_format($order->product->price * $order->quantity, 2) : '-' }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ ucfirst($order->status) }}</dd>
                <dt class="col-sm-3">Due Date</dt>
                <dd class="col-sm-9">{{ $order->due_date ? \Illuminate\Support\Carbon::parse($order->due_date)->format('Y-m-d') : '-' }}</dd>
                <dt class="col-sm-3">Notes</dt>
                <dd class="col-sm-9">{{ $order->notes }}</dd>
            </dl>
            <div class="mt-4">
                @if($order->status === 'pending')
                    <form method="POST" action="{{ route('manufacturer.production-orders.accept', $order->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('manufacturer.production-orders.reject', $order->id) }}" class="d-inline ms-2">
                        @csrf
                        <input type="text" name="reason" placeholder="Rejection reason" class="form-control d-inline w-auto" style="display:inline-block; width:200px;">
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </form>
                @elseif(in_array($order->status, ['ready_to_ship', 'shipped']))
                    <form method="POST" action="{{ route('manufacturer.production-orders.deliver', $order->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Mark as Delivered</button>
                    </form>
                @elseif($order->status === 'in_production')
                    <form method="POST" action="{{ route('manufacturer.production-orders.complete', $order->id) }}" class="d-inline">
                        @csrf
                        <input type="number" name="produced_quantity" value="{{ $order->quantity }}" min="1" max="{{ $order->quantity }}" class="form-control d-inline w-auto" style="display:inline-block; width:120px;">
                        <button type="submit" class="btn btn-success">Complete Production</button>
                    </form>
                @elseif($order->status === 'completed')
                    <form method="POST" action="{{ route('manufacturer.production-orders.ship', $order->id) }}" class="d-inline">
                        @csrf
                        <input type="text" name="tracking_number" placeholder="Tracking Number" class="form-control d-inline w-auto" style="display:inline-block; width:180px;">
                        <input type="date" name="shipment_date" class="form-control d-inline w-auto" style="display:inline-block; width:150px;">
                        <button type="submit" class="btn btn-info">Mark as Shipped</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <a href="{{ route('manufacturer.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>
@endsection 