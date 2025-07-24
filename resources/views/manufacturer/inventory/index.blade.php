@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Inventory Management</h2>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Raw Materials</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Reorder Point</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rawMaterials as $material)
                                    <tr @if(($material->delivered_quantity ?? 0) <= ($material->min_stock_level ?? 0)) style="background: #fff3cd;" @endif>
                                        <td>{{ $material->name }}</td>
                                        <td>{{ $material->delivered_quantity ?? 0 }}</td>
                                        <td>{{ $material->unit ?? '-' }}</td>
                                        <td>{{ $material->min_stock_level ?? '-' }}</td>
                                        <td>
                                            @if(($material->delivered_quantity ?? 0) <= ($material->min_stock_level ?? 0))
                                                <span class="badge bg-warning text-dark">Low Stock</span>
                                            @else
                                                <span class="badge bg-success">OK</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('manufacturer.inventory.editRawMaterial', $material->id) }}" class="btn btn-sm btn-outline-primary">Update</a>
                                            <a href="{{ route('manufacturer.purchase-orders') }}" class="btn btn-sm btn-outline-success">Order More</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">No raw materials found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                    <h5 class="mb-0">Finished Products (Clothes)</h5>
                    <a href="{{ route('manufacturer.products.create') }}" class="btn btn-sm btn-success">Add Product</a>
                </div>
                <div class="card-body">
                    @if(isset($finishedProducts) && $finishedProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Min Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($finishedProducts as $product)
                                    @php
                                        $stock = $product->inventory->quantity ?? 0;
                                        $minStock = $product->min_stock_level ?? 500;
                                        $isLow = $stock < $minStock;
                                    @endphp
                                    <tr @if($isLow) style="background-color: #fff3cd;" @endif>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="Image" style="max-width: 60px;">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category ?? '-' }}</td>
                                        <td>{{ $product->description }}</td>
                                        <td>{{ $product->price ? ('$' . number_format($product->price, 2)) : '-' }}</td>
                                        <td>{{ $stock }}</td>
                                        <td>{{ $minStock }}</td>
                                        <td>
                                            @if($isLow)
                                                <span class="badge bg-warning text-dark">Low</span>
                                            @else
                                                <span class="badge bg-success">OK</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('manufacturer.products.edit', $product->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('manufacturer.products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">No finished products found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@php
    $logs = \App\Models\InventoryChangeLog::with(['product', 'user'])->latest()->limit(20)->get();
@endphp
<div class="card mt-5">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Recent Inventory Changes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Change</th>
                        <th>User</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $log->product->name ?? '-' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $log->item_type)) }}</td>
                            <td>{{ $log->old_quantity }} → <strong>{{ $log->new_quantity }}</strong></td>
                            <td>{{ $log->user->name ?? 'User#'.$log->user_id }}</td>
                            <td>{{ $log->note ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No inventory changes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 