@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Edit Order</h2>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST" action="{{ route('retailer.orders.update', $order->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-control" value="{{ $order->product->name ?? 'N/A' }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity', $order->quantity) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $order->due_date ? $order->due_date->format('Y-m-d') : '' ) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $order->notes) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Address</label>
                    <input type="text" name="shipping_address" class="form-control" value="{{ old('shipping_address', $order->shipping_address) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping City</label>
                    <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city', $order->shipping_city) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping State</label>
                    <input type="text" name="shipping_state" class="form-control" value="{{ old('shipping_state', $order->shipping_state) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Zip</label>
                    <input type="text" name="shipping_zip" class="form-control" value="{{ old('shipping_zip', $order->shipping_zip) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Country</label>
                    <input type="text" name="shipping_country" class="form-control" value="{{ old('shipping_country', $order->shipping_country) }}" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Update Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 