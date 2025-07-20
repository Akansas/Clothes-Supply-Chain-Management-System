<h2>Daily Supplier Report</h2>
<p>Hello {{ $supplier->name }},</p>

<h3>Inventory Status</h3>
@if($inventory->count())
    <ul>
        @foreach($inventory as $item)
            <li>{{ $item->product->name ?? 'Product' }}: {{ $item->quantity }} in stock</li>
        @endforeach
    </ul>
@else
    <p>No inventory records found.</p>
@endif

<h3>Pending Orders</h3>
@if($pendingOrders->count())
    <ul>
        @foreach($pendingOrders as $order)
            <li>Order #{{ $order->id }} - {{ $order->status }} - {{ $order->created_at->format('Y-m-d') }}</li>
        @endforeach
    </ul>
@else
    <p>No pending orders.</p>
@endif

<p>Best regards,<br>Supply Chain Management System</p>