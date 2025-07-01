@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="fw-bold mb-0">Add New Material</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier.materials.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Material Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="material" class="form-label">Material Type (e.g., Cotton, Polyester)</label>
                                <input type="text" class="form-control" id="material" name="material" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category (e.g., Fabric, Thread)</label>
                                <input type="text" class="form-control" id="category" name="category" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price (Selling Price)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cost" class="form-label">Cost (Your Cost)</label>
                                <input type="number" class="form-control" id="cost" name="cost" step="0.01" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unit" class="form-label">Unit (e.g., m², kg, spool)</label>
                                <input type="text" class="form-control" id="unit" name="unit" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stock_quantity" class="form-label">Initial Stock Quantity</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="0" required>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Available for ordering</label>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('supplier.materials.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 