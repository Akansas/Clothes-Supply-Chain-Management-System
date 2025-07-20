@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Retail Store Inventory</h2>
    <div class="card mx-auto mb-4" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST" action="{{ route('retailer.inventory.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Add to Inventory</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mx-auto" style="max-width: 900px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Min Stock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                            @php
                                $minStock = $item->product->min_stock_level ?? 10;
                                $isLow = $item->quantity < $minStock;
                            @endphp
                            <tr @if($isLow) style="background-color: #fff3cd;" @endif>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $minStock }}</td>
                                <td>
                                    @if($isLow)
                                        <span class="badge bg-warning text-dark">Low</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('retailer.inventory.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('retailer.inventory.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No inventory items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $inventory->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 