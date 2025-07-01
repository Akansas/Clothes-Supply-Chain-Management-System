@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Order Raw Material</h4>
                </div>
                <div class="card-body">
                    <h5>{{ $material->name }}</h5>
                    <p>{{ $material->description }}</p>
                    <p><strong>Supplier:</strong> {{ $material->supplier->user->name ?? 'N/A' }}</p>
                    <p><strong>Price:</strong> ${{ number_format($material->price, 2) }} / {{ $material->unit }}</p>
                    <form action="{{ route('manufacturer.materials.order.place', $material->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-success">Place Order</button>
                        <a href="{{ route('manufacturer.materials.browse') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 