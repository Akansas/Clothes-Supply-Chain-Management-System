@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Purchase Order #{{ $order->id }}</h5>
                    <a href="{{ route('manufacturer.purchase-orders') }}" class="btn btn-sm btn-secondary">Back to Orders</a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Order Number</dt>
                        <dd class="col-sm-9">{{ $order->order_number }}</dd>
                        <dt class="col-sm-3">Supplier</dt>
                        <dd class="col-sm-9">{{ $order->supplier ? $order->supplier->user->name : 'N/A' }}</dd>
                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            <span class="badge {{ method_exists($order, 'getStatusBadgeClass') ? $order->getStatusBadgeClass() : 'badge-secondary' }}">
                                {{ method_exists($order, 'getStatusText') ? $order->getStatusText() : ($order->status ?? 'N/A') }}
                            </span>
                        </dd>
                        <dt class="col-sm-3">Order Date</dt>
                        <dd class="col-sm-9">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                        <dt class="col-sm-3">Total Amount</dt>
                        <dd class="col-sm-9">${{ number_format($order->total_amount, 2) }}</dd>
                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $order->notes ?? '-' }}</dd>
                    </dl>
                    <h6>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product ? $item->product->name : 'N/A' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
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