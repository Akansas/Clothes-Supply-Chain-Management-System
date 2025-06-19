@extends('adminlte::page')

@section('title', 'Supplier Dashboard')

@section('content_header')
    <h1>Supplier Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Orders</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><a href="{{ route('supplier.orders') }}">{{ $order->order_id }}</a></td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td><span class="badge bg-{{ $order->status == 'completed' ? 'success' : 'warning' }}">{{ ucfirst($order->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Shipments</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Shipment ID</th>
                                <th>Destination</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipments as $shipment)
                            <tr>
                                <td><a href="{{ route('supplier.shipments') }}">{{ $shipment->shipment_id }}</a></td>
                                <td>{{ $shipment->warehouse->location }}</td>
                                <td>{{ $shipment->expected_delivery_date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop