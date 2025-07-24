@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Edit Finished Product</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('manufacturer.products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" @if(old('category', $product->category) == $cat) selected @endif>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}">
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity / Stock</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', $quantity) }}" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="min_stock_level" class="form-label">Min Stock Level</label>
                            <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" min="0" value="{{ old('min_stock_level', $product->min_stock_level ?? 500) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Update</label>
                            <input type="text" class="form-control" id="reason" name="reason" value="{{ old('reason') }}">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" style="max-width: 120px;">
                                </div>
                            @endif
                        </div>
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <a href="{{ route('manufacturer.dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 