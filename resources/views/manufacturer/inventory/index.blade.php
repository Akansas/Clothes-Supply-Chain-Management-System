@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Inventory Management</h2>
    <div class="mb-3">
        <a href="{{ route('inventory.adjustments.create') }}" class="btn btn-outline-secondary">Adjust Inventory</a>
        <a href="{{ route('inventory.adjustments.index') }}" class="btn btn-outline-info">View Adjustments</a>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Raw Materials</h5>
                    <p class="card-text display-6">{{ $rawMaterials->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Suppliers</h5>
                    <p class="card-text display-6">{{ $suppliers->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">WIP</h5>
                    <p class="card-text display-6">{{ $wip->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Finished Goods</h5>
                    <p class="card-text display-6">{{ $finishedGoods->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Raw Materials</h4>
        <a href="{{ route('inventory.raw-materials.create') }}" class="btn btn-primary">Add Raw Material</a>
    </div>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Quantity</th><th>Unit</th><th>Reorder Level</th><th>Status</th><th>Supplier</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($rawMaterials as $mat)
            <tr>
                <td>{{ $mat->name }}</td>
                <td>{{ $mat->quantity }}</td>
                <td>{{ $mat->unit }}</td>
                <td>{{ $mat->reorder_level }}</td>
                <td>{{ $mat->status }}</td>
                <td>{{ $mat->supplier->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('inventory.raw-materials.edit', $mat->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('inventory.raw-materials.destroy', $mat->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this raw material?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Suppliers</h4>
        <a href="{{ route('inventory.suppliers.create') }}" class="btn btn-primary">Add Supplier</a>
    </div>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Contact</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($suppliers as $sup)
            <tr>
                <td>{{ $sup->name }}</td>
                <td>{{ $sup->contact_info }}</td>
                <td>{{ $sup->email }}</td>
                <td>{{ $sup->phone }}</td>
                <td>
                    <a href="{{ route('inventory.suppliers.edit', $sup->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('inventory.suppliers.destroy', $sup->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this supplier?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Incoming Shipments</h4>
        <a href="{{ route('inventory.incoming-shipments.create') }}" class="btn btn-primary">Add Incoming Shipment</a>
    </div>
    <table class="table table-bordered">
        <thead><tr><th>Material</th><th>Supplier</th><th>Quantity</th><th>Expected</th><th>Received</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($incomingShipments as $ship)
            <tr>
                <td>{{ $ship->rawMaterial->name ?? 'N/A' }}</td>
                <td>{{ $ship->supplier->name ?? 'N/A' }}</td>
                <td>{{ $ship->quantity }}</td>
                <td>{{ $ship->expected_date }}</td>
                <td>{{ $ship->received_date }}</td>
                <td>{{ $ship->status }}</td>
                <td>
                    <a href="{{ route('inventory.incoming-shipments.edit', $ship->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('inventory.incoming-shipments.destroy', $ship->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this shipment?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <h4>Work In Progress (WIP)</h4>
    <table class="table table-bordered">
        <thead><tr><th>Product</th><th>Stage</th><th>Quantity</th><th>Started</th><th>Expected Completion</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($wip as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->stage }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->started_at }}</td>
                <td>{{ $item->expected_completion }}</td>
                <td>{{ $item->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <h4>Finished Goods</h4>
    <table class="table table-bordered">
        <thead><tr><th>Product</th><th>Quantity</th><th>Location</th><th>Status</th><th>Ready for Shipment</th><th>Damaged</th><th>Returned</th></tr></thead>
        <tbody>
        @foreach($finishedGoods as $fg)
            <tr>
                <td>{{ $fg->product_name }}</td>
                <td>{{ $fg->quantity }}</td>
                <td>{{ $fg->location }}</td>
                <td>{{ $fg->status }}</td>
                <td>{{ $fg->ready_for_shipment ? 'Yes' : 'No' }}</td>
                <td>{{ $fg->damaged ? 'Yes' : 'No' }}</td>
                <td>{{ $fg->returned ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <h4>Recent Inventory Activity</h4>
    <table class="table table-bordered">
        <thead><tr><th>Type</th><th>Item ID</th><th>Action</th><th>Quantity</th><th>User</th><th>Reason</th><th>Date</th></tr></thead>
        <tbody>
        @foreach($inventoryLogs as $log)
            <tr>
                <td>{{ $log->item_type }}</td>
                <td>{{ $log->item_id }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->quantity }}</td>
                <td>{{ $log->user->name ?? 'N/A' }}</td>
                <td>{{ $log->reason }}</td>
                <td>{{ $log->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection 