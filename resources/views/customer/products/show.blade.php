@extends('layouts.app')
@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.products.browse') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            <!-- Product Image -->
            <div class="card">
                <div class="card-body text-center">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" class="img-fluid" style="max-height: 400px;" alt="{{ $product->name }}">
                    @else
                        <div class="bg-light py-5">
                            <span class="text-muted">No Image Available</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Product Details -->
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ $product->name }}</h2>
                    <p class="text-muted">by {{ $product->vendor->company_name ?? 'Unknown Vendor' }}</p>
                    
                    <div class="mb-3">
                        <span class="h3 text-primary">${{ number_format($product->price, 2) }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <p class="card-text">{{ $product->description }}</p>
                    </div>
                    
                    <!-- Product Specifications -->
                    @if($product->specifications)
                    <div class="mb-3">
                        <h6>Specifications:</h6>
                        <ul class="list-unstyled">
                            @foreach(json_decode($product->specifications, true) ?? [] as $key => $value)
                                <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <!-- Add to Cart Form -->
                    <form method="POST" action="{{ route('customer.cart.add', $product->id) }}" class="mb-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="99">
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">Add to Cart</button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Vendor Information -->
                    @if($product->vendor)
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Vendor Information</h6>
                            <p class="mb-1"><strong>Company:</strong> {{ $product->vendor->company_name }}</p>
                            <p class="mb-1"><strong>Contact:</strong> {{ $product->vendor->contact_person }}</p>
                            <p class="mb-0"><strong>Email:</strong> {{ $product->vendor->email }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3>Related Products</h3>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-img-top bg-light text-center py-3" style="height: 150px;">
                            @if($relatedProduct->image_url)
                                <img src="{{ $relatedProduct->image_url }}" class="img-fluid" style="max-height: 130px;" alt="{{ $relatedProduct->name }}">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                            <p class="text-primary mb-2">${{ number_format($relatedProduct->price, 2) }}</p>
                            <a href="{{ route('customer.products.show', $relatedProduct->id) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 