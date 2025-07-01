@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="fw-bold mb-0">Update Stock</h2>
                    <p class="text-muted">For material: {{ $material->name }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier.materials.stock.update', $material) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">New Stock Quantity</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ $material->stock_quantity }}" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('supplier.materials.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Stock</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 