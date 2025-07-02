@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Create New Production Order</h2>
            <a href="{{ route('manufacturer.production-orders') }}" class="btn btn-outline-secondary mb-3">Back to Orders</a>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('manufacturer.production-orders.store') }}" method="POST">
        @csrf
        <div class="card mb-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Expected Completion</label>
                        <input type="date" name="expected_completion" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Create Production Order</button>
    </form>
</div>
@endsection 