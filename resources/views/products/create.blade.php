@extends('layouts.app', ['activePage' => 'retailer_inventory', 'title' => 'Add Product', 'navName' => 'Add Product', 'activeButton' => 'retailer'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Product</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="sku">SKU</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" name="stock" class="form-control" required min="0">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                        <a href="{{ route('retailer.inventory') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 