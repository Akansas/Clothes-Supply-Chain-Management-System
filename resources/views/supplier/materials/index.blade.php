@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Material Catalog</h2>
            <p class="text-muted">Manage your raw materials available for manufacturers.</p>
        </div>
        <a href="{{ route('supplier.materials.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Material
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price / Unit</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                        <tr>
                            <td>{{ $material->name }}</td>
                            <td><span class="badge bg-secondary">{{ $material->category }}</span></td>
                            <td>${{ number_format($material->price, 2) }} / {{ $material->unit }}</td>
                            <td>{{ $material->stock_quantity }}</td>
                            <td>
                                @if($material->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('supplier.materials.stock.edit', $material) }}" class="btn btn-sm btn-outline-success">Update Stock</a>
                                <a href="{{ route('supplier.materials.edit', $material) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('supplier.materials.destroy', $material) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">You have not added any materials yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $materials->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 