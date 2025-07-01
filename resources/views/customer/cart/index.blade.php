@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Shopping Cart</h2>
        <a href="{{ route('customer.products.browse') }}" class="btn btn-outline-primary">Continue Shopping</a>
    </div>

    @if(count($cartItems) > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Cart Items ({{ count($cartItems) }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                        <div class="row align-items-center mb-3 pb-3 border-bottom">
                            <div class="col-md-2">
                                <div class="bg-light text-center py-2" style="height: 80px;">
                                    @if($item['product']->image_url)
                                        <img src="{{ $item['product']->image_url }}" class="img-fluid" style="max-height: 70px;" alt="{{ $item['product']->name }}">
                                    @else
                                        <span class="text-muted small">No Image</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="mb-1">{{ $item['product']->name }}</h6>
                                <small class="text-muted">by {{ $item['product']->vendor->company_name ?? 'Unknown' }}</small>
                            </div>
                            <div class="col-md-2">
                                <span class="text-primary">${{ number_format($item['product']->price, 2) }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="badge bg-secondary">{{ $item['quantity'] }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="fw-bold">${{ number_format($item['subtotal'], 2) }}</span>
                            </div>
                            <div class="col-md-12 mt-2">
                                <form method="POST" action="{{ route('customer.cart.remove', $item['product']->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                </form>
                                <a href="{{ route('customer.products.show', $item['product']->id) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>${{ number_format($total * 0.1, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong>${{ number_format($total * 1.1, 2) }}</strong>
                        </div>
                        <a href="{{ route('customer.checkout') }}" class="btn btn-success w-100">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <h4 class="text-muted">Your cart is empty</h4>
            <p class="text-muted">Add some products to your cart to get started!</p>
            <a href="{{ route('customer.products.browse') }}" class="btn btn-primary">Browse Products</a>
        </div>
    @endif
</div>
@endsection 