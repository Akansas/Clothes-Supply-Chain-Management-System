@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Edit Purchase Order</h2>
            <p class="text-muted">Update shipping, billing, or item quantities for this order.</p>
        </div>
        <a href="{{ route('manufacturer.purchase-orders') }}" class="btn btn-outline-secondary">Back to Orders</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manufacturer.purchase-orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card mb-4">
            <div class="card-body">
                <h5>Shipping Address</h5>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Address</label>
                        <input type="text" name="shipping_address" class="form-control" value="{{ old('shipping_address', $order->shipping_address) }}" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">City</label>
                        <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city', $order->shipping_city) }}" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">State</label>
                        <input type="text" name="shipping_state" class="form-control" value="{{ old('shipping_state', $order->shipping_state) }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">ZIP</label>
                        <input type="text" name="shipping_zip" class="form-control" value="{{ old('shipping_zip', $order->shipping_zip) }}" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Country</label>
                        <input type="text" name="shipping_country" class="form-control" value="{{ old('shipping_country', $order->shipping_country) }}" required>
                    </div>
                </div>
                <h5>Billing Address</h5>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Billing Address</label>
                        <input type="text" name="billing_address" class="form-control" value="{{ old('billing_address', $order->billing_address) }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Order Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product->name ?? '-' }}</td>
                                <td>{{ $item->product->description ?? '-' }}</td>
                                <td>{{ $item->product->unit ?? '-' }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>
                                    <input type="number" name="order_items[{{ $item->id }}][quantity]" class="form-control" value="{{ old('order_items.' . $item->id . '.quantity', $item->quantity) }}" min="1" required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
</div>
@endsection 