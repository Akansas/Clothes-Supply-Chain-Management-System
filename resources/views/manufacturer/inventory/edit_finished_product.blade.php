@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Finished Product Stock</h2>
    <form method="POST" action="{{ route('manufacturer.inventory.updateFinishedProduct', $product->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" value="{{ $product->name }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" value="{{ $product->stock_quantity ?? $product->quantity ?? 0 }}" min="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('manufacturer.inventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 