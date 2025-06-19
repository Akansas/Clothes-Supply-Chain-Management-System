@extends('adminlte::page')

@section('title', 'Supplier Orders')

@section('content_header')
    <h1>Order Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Orders</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Search orders...">
                    <div class="input-group-append">
                        <button class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Products</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            @foreach($order->items as $item)
                                {{ $item->product->name }}<br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($order->items as $item)
                                {{ $item->quantity }}<br>
                            @endforeach
                        </td>
                        <td><span class="badge bg-{{ $order->status == 'completed' ? 'success' : 'warning' }}">{{ ucfirst($order->status) }}</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($order->status == 'pending')
                            <a href="#" class="btn btn-sm btn-success" title="Mark as Shipped">
                                <i class="fas fa-truck"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
@stop