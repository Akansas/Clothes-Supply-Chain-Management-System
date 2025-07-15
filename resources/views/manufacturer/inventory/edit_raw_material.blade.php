@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Raw Material</h2>
    <form action="{{ route('inventory.raw-materials.update', $rawMaterial->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $rawMaterial->name }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control">{{ $rawMaterial->description }}</textarea>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $rawMaterial->quantity }}" required min="0">
        </div>
        <div class="mb-3">
            <label for="reorder_level" class="form-label">Reorder Level</label>
            <input type="number" name="reorder_level" id="reorder_level" class="form-control" value="{{ $rawMaterial->reorder_level }}" required min="0">
        </div>
        <div class="mb-3">
            <label for="unit" class="form-label">Unit</label>
            <input type="text" name="unit" id="unit" class="form-control" value="{{ $rawMaterial->unit }}" required>
        </div>
        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control">
                <option value="">None</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @if($rawMaterial->supplier_id == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available" @if($rawMaterial->status == 'available') selected @endif>Available</option>
                <option value="low" @if($rawMaterial->status == 'low') selected @endif>Low</option>
                <option value="out" @if($rawMaterial->status == 'out') selected @endif>Out</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Raw Material</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 