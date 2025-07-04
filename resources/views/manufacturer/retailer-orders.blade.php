@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Retailer Production Orders</h2>
    <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        Manufacturer User ID: {{ auth()->user()->id }}<br>
        Manufacturer Profile ID: {{ auth()->user()->manufacturer_id ?? 'N/A' }}<br>
        <br>
        Production Orders in DB:<br>
        <ul>
            @foreach(\App\Models\ProductionOrder::all() as $po)
                <li>
                    ID: {{ $po->id }}, manufacturer_id: {{ $po->manufacturer_id }}, retailer_id: {{ $po->retailer_id }}, product_id: {{ $po->product_id }}
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card">
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Retailer</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->retailer->name ?? 'N/A' }}</td>
                                <td>{{ $order->product->name ?? 'N/A' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>{{ $order->due_date ? \Illuminate\Support\Carbon::parse($order->due_date)->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <a href="{{ route('manufacturer.production-orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $orders->links() }}
                </div>
            @else
                <p class="text-muted">No production orders from retailers found.</p>
            @endif
        </div>
    </div>
    <a href="{{ route('manufacturer.dashboard') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
@endsection 