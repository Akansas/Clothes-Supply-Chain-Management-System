@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Incoming Shipment</h2>
    <form action="{{ route('inventory.incoming-shipments.update', $shipment->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="raw_material_id" class="form-label">Raw Material</label>
            <select name="raw_material_id" id="raw_material_id" class="form-control" required>
                @foreach($rawMaterials as $mat)
                    <option value="{{ $mat->id }}" @if($shipment->raw_material_id == $mat->id) selected @endif>{{ $mat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control">
                <option value="">None</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @if($shipment->supplier_id == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $shipment->quantity }}" required min="1">
        </div>
        <div class="mb-3">
            <label for="expected_date" class="form-label">Expected Date</label>
            <input type="date" name="expected_date" id="expected_date" class="form-control" value="{{ $shipment->expected_date }}">
        </div>
        <div class="mb-3">
            <label for="received_date" class="form-label">Received Date</label>
            <input type="date" name="received_date" id="received_date" class="form-control" value="{{ $shipment->received_date }}">
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="pending" @if($shipment->status == 'pending') selected @endif>Pending</option>
                <option value="received" @if($shipment->status == 'received') selected @endif>Received</option>
                <option value="cancelled" @if($shipment->status == 'cancelled') selected @endif>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Shipment</button>
        <a href="{{ route('manufacturer.inventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 