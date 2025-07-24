@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Edit Inventory Item</h2>
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <form method="POST" action="{{ route('retailer.inventory.update', $inventory->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-control" value="{{ $inventory->product->name ?? 'N/A' }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="0" value="{{ $inventory->quantity }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Min Stock Level</label>
                    <input type="number" name="min_stock_level" class="form-control" min="0" value="{{ $inventory->product->min_stock_level ?? 10 }}" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Update Inventory</button>
                    <a href="{{ route('retailer.inventory') }}" class="btn btn-secondary mt-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 