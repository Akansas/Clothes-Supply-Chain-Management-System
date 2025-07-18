@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <!-- Debug Info -->
    <div class="alert alert-info mb-4">
        <strong>Debug Info:</strong><br>
        Current User ID: {{ auth()->id() }}<br>
        Orders for this user: {{ $productionOrders->total() }}
    </div>
    @if($productionOrders->total() === 0 && isset($allProductionOrders))
        <div class="alert alert-warning">
            <strong>No orders found for this user.</strong><br>
            <span>Showing all orders for debugging:</span>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order #</th>
                                <th>Retailer ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allProductionOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->retailer_id }}</td>
                                    <td>{{ optional($order->product)->name }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    <!-- End Debug Info -->
    <h2 class="fw-bold mb-4 text-center">Orders to Manufacturers</h2>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @include('retailer.orders._orders-table', ['orders' => $productionOrders])
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $productionOrders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 