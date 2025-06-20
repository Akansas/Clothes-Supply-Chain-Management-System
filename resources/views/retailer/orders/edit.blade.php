@extends('layouts.app', ['activePage' => 'retailer_orders', 'title' => 'Edit Order', 'navName' => 'Edit Order', 'activeButton' => 'retailer'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Order</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select name="product_id" class="form-control" required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @if($order->product_id == $product->id) selected @endif>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="{{ $order->quantity }}" required min="1">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Pending" @if($order->status == 'Pending') selected @endif>Pending</option>
                                <option value="Shipped" @if($order->status == 'Shipped') selected @endif>Shipped</option>
                                <option value="Cancelled" @if($order->status == 'Cancelled') selected @endif>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ $order->customer_name }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Order</button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 