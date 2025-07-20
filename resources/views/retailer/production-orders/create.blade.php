@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Place Order</h2>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST" action="{{ route('retailer.production-orders.store') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-control" value="{{ $product->name }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" class="form-control" value="{{ $product->manufacturer->name ?? 'Not Available' }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Address</label>
                    <input type="text" name="shipping_address" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping City</label>
                    <input type="text" name="shipping_city" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping State</label>
                    <input type="text" name="shipping_state" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Zip</label>
                    <input type="text" name="shipping_zip" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Country</label>
                    <input type="text" name="shipping_country" class="form-control" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Submit Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 