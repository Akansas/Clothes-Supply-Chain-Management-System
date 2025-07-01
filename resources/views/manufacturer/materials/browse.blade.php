@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Browse Raw Materials</h2>
            <p class="lead text-muted">Find and order raw materials from available suppliers.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Available Materials</h5>
        </div>
        <div class="card-body">
            @if($materials->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Material Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materials as $material)
                            <tr>
                                <td>{{ $material->name }}</td>
                                <td><span class="badge bg-secondary">{{ $material->category }}</span></td>
                                <td>{{ $material->supplier->user->name ?? 'N/A' }}</td>
                                <td>${{ number_format($material->price, 2) }} / {{ $material->unit }}</td>
                                <td>
                                    <a href="{{ route('manufacturer.materials.order', $material->id) }}" class="btn btn-sm btn-success">Order Now</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $materials->links() }}
                </div>
            @else
                <p class="text-muted text-center">No raw materials are available at the moment.</p>
            @endif
        </div>
    </div>
</div>
@endsection 