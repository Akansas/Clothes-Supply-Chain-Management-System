@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Checkout</h2>

    <form method="POST" action="{{ route('customer.checkout.place-order') }}">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Billing Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="billing_address" class="form-label">Billing Address</label>
                            <textarea name="billing_address" id="billing_address" class="form-control" rows="3" required>{{ old('billing_address', auth()->user()->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                <label class="form-check-label" for="credit_card">
                                    Credit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">
                                    PayPal
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                <label class="form-check-label" for="bank_transfer">
                                    Bank Transfer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Order Summary -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <strong>{{ $item['product']->name }}</strong>
                                <br>
                                <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                            </div>
                            <span>${{ number_format($item['subtotal'], 2) }}</span>
                        </div>
                        @endforeach
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (10%):</span>
                            <span>${{ number_format($total * 0.1, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong>${{ number_format($total * 1.1, 2) }}</strong>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                        <a href="{{ route('customer.cart') }}" class="btn btn-outline-secondary w-100 mt-2">Back to Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection 