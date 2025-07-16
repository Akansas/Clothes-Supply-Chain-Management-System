@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Raw Material Stock</h2>
    <form method="POST" action="{{ route('manufacturer.inventory.updateRawMaterial', $material->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" value="{{ $material->name }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" value="{{ $material->stock_quantity ?? $material->quantity ?? 0 }}" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Reorder Point <span data-bs-toggle="tooltip" title="The inventory level at which you should order more to avoid running out. Set based on your usage and supplier lead time."><i class="fas fa-info-circle"></i></span></label>
            <input type="number" name="min_stock_level" class="form-control" value="{{ $material->min_stock_level ?? 0 }}" min="0" required>
            <small class="form-text text-muted">When delivered quantity drops to or below this value, you'll see a low stock warning.</small>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('manufacturer.inventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 