@extends('layouts.app', ['activePage' => 'retailer_orders', 'title' => 'Orders', 'navName' => 'Orders', 'activeButton' => 'retailer'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Orders</h4>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">Place New Order</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->product->name ?? 'N/A' }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>
                                        <form action="{{ route('orders.update', $order->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="this.form.submit()" class="form-control form-control-sm d-inline-block w-auto">
                                                <option value="Pending" @if($order->status == 'Pending') selected @endif>Pending</option>
                                                <option value="Shipped" @if($order->status == 'Shipped') selected @endif>Shipped</option>
                                                <option value="Cancelled" @if($order->status == 'Cancelled') selected @endif>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="product_id" value="{{ $order->product_id }}">
                                            <input type="hidden" name="quantity" value="{{ $order->quantity }}">
                                            <input type="hidden" name="customer_name" value="{{ $order->customer_name }}">
                                        </form>
                                        @if($order->status == 'Pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($order->status == 'Shipped')
                                            <span class="badge badge-success">Shipped</span>
                                        @elseif($order->status == 'Cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>
                                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 