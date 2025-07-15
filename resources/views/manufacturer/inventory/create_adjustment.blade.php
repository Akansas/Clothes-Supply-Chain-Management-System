@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Inventory Adjustment</h2>
    <form action="{{ route('inventory.adjustments.store') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="raw_material_id" class="form-label">Raw Material</label>
            <select name="raw_material_id" id="raw_material_id" class="form-select">
                <option value="">-- None --</option>
                @foreach($rawMaterials as $mat)
                    <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">Select a raw material or a finished good below (not both).</small>
        </div>
        <div class="mb-3">
            <label for="finished_good_id" class="form-label">Finished Good</label>
            <select name="finished_good_id" id="finished_good_id" class="form-select">
                <option value="">-- None --</option>
                @foreach($finishedGoods as $fg)
                    <option value="{{ $fg->id }}">{{ $fg->product_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="adjustment_type" class="form-label">Adjustment Type</label>
            <select name="adjustment_type" id="adjustment_type" class="form-select" required>
                <option value="increase">Increase</option>
                <option value="decrease">Decrease</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
            <label for="reason" class="form-label">Reason</label>
            <input type="text" name="reason" id="reason" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit Adjustment</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 