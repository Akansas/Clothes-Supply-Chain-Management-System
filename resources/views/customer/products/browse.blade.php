@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Browse Products</h2>
        <a href="{{ route('customer.cart') }}" class="btn btn-outline-primary">
            <i class="fas fa-shopping-cart"></i> Cart
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customer.products.browse') }}">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="vendor_id" class="form-control">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @if(request('vendor_id') == $vendor->id) selected @endif>{{ $vendor->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="{{ request('min_price') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="{{ request('max_price') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-img-top bg-light text-center py-4" style="height: 200px;">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" class="img-fluid" style="max-height: 180px;" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <span class="text-muted">No Image</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 text-primary mb-0">${{ number_format($product->price, 2) }}</span>
                                <small class="text-muted">by {{ $product->vendor->company_name ?? 'Unknown' }}</small>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.products.show', $product->id) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                                <form method="POST" action="{{ route('customer.cart.add', $product->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <h4 class="text-muted">No products found</h4>
            <p class="text-muted">Try adjusting your search criteria or browse all products.</p>
            <a href="{{ route('customer.products.browse') }}" class="btn btn-primary">View All Products</a>
        </div>
    @endif
</div>
@endsection 