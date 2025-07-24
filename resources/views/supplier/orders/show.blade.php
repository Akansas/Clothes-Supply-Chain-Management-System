@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Order Details</h2>
            <p class="text-muted">Detailed view of the order placed by the manufacturer.</p>
        </div>
        <a href="{{ route('supplier.orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Order Information</h5>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Order ID:</strong> #{{ $order->id }}</li>
                        <li><strong>Order Number:</strong> {{ $order->order_number ?? '-' }}</li>
                        <li><strong>Status:</strong> <span class="badge {{ $order->getStatusBadgeClass() }}">{{ $order->getStatusText() }}</span></li>
                        <li><strong>Order Date:</strong> {{ $order->order_date ? $order->order_date->format('M d, Y') : ($order->created_at ? $order->created_at->format('M d, Y') : '-') }}</li>
                        <li><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</li>
                        <li><strong>Tax:</strong> ${{ number_format($order->tax_amount, 2) }}</li>
                        <li><strong>Shipping:</strong> ${{ number_format($order->shipping_amount, 2) }}</li>
                        <li><strong>Payment Method:</strong> {{ $order->payment_method ?? '-' }}</li>
                        <li><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? '-') }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Manufacturer Information</h5>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Name:</strong> {{ $order->manufacturer->name ?? '-' }}</li>
                        <li><strong>Email:</strong> {{ $order->manufacturer->email ?? '-' }}</li>
                        <li><strong>Phone:</strong> {{ $order->manufacturer->phone ?? '-' }}</li>
                        <li><strong>Company:</strong> {{ $order->manufacturer->company_name ?? '-' }}</li>
                        <li><strong>Address:</strong> {{ $order->manufacturer->address ?? '-' }}</li>
                    </ul>
                    <a href="{{ route('chat.with', ['user' => $order->manufacturer->user_id]) }}" class="btn btn-primary btn-sm mt-2">Message</a>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Shipping Address</h5>
                    <address>
                        {{ $order->shipping_address ?? '-' }}<br>
                        {{ $order->shipping_city ?? '' }} {{ $order->shipping_state ?? '' }}<br>
                        {{ $order->shipping_zip ?? '' }} {{ $order->shipping_country ?? '' }}
                    </address>
                </div>
                <div class="col-md-6">
                    <h5>Billing Address</h5>
                    <address>
                        {{ $order->billing_address ?? '-' }}
                    </address>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Order Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->product->description ?? '-' }}</td>
                            <td>{{ $item->product->unit ?? '-' }}</td>
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

    @if($order->deliveries && $order->deliveries->count())
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Delivery Information</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Delivery ID</th>
                            <th>Status</th>
                            <th>Driver</th>
                            <th>Warehouse</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->id }}</td>
                            <td>{{ ucfirst($delivery->status) }}</td>
                            <td>{{ $delivery->driver->name ?? '-' }}</td>
                            <td>{{ $delivery->warehouse->name ?? '-' }}</td>
                            <td>{{ $delivery->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($order->notes)
    <div class="card mb-4">
        <div class="card-body">
            <h5>Order Notes</h5>
            <p>{{ $order->notes }}</p>
        </div>
    </div>
    @endif

    @if($order->status === 'pending' || $order->status === 'confirmed')
        <div class="card mb-4">
            <div class="card-body">
                <h5>Assign Delivery Personnel & Ship Order</h5>
                <form action="{{ route('supplier.orders.ship', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="driver_id" class="form-label">Select Delivery Personnel</label>
                        <select name="driver_id" id="driver_id" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach($deliveryPersonnel ?? [] as $person)
                                <option value="{{ $person->id }}">{{ $person->name }} ({{ $person->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning">Ship Order</button>
                </form>
            </div>
        </div>
    @endif
    @if($order->status === 'in_transit' || $order->status === 'delivered')
        <div class="card mb-4">
            <div class="card-body">
                <h5>Delivery Status</h5>
                @php $delivery = $order->deliveries->first(); @endphp
                <p>Status: <span class="badge bg-info">{{ ucfirst($delivery->status ?? $order->status) }}</span></p>
                <p>Assigned To: {{ $delivery->driver->name ?? 'N/A' }}</p>
            </div>
        </div>
    @endif

    @php $delivery = $order->deliveries->last(); @endphp
    @if($delivery)
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Delivery Tracking</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-3">
                    <li><strong>Tracking Number:</strong> {{ $delivery->tracking_number }}</li>
                    <li><strong>Status:</strong> <span class="badge bg-info">{{ ucfirst($delivery->status) }}</span></li>
                    <li><strong>Assigned Personnel:</strong> {{ $delivery->driver->name ?? 'N/A' }}</li>
                    <li><strong>Shipped At:</strong> {{ $delivery->created_at->format('M d, Y H:i') }}</li>
                    <li><strong>Delivered At:</strong> {{ $delivery->delivered_at ? $delivery->delivered_at->format('M d, Y H:i') : '-' }}</li>
                </ul>
                @if(!in_array($delivery->status, ['delivered', 'cancelled']))
                    <form action="{{ route('supplier.deliveries.cancel', $delivery) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this shipment?');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger">Cancel Shipment</button>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection 