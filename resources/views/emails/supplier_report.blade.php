<h2>Today's Purchase Orders</h2>

@foreach ($orders as $order)
    <div style="margin-bottom: 10px;">
        <strong>Product:</strong> {{ $order->product->name }}<br>
        <strong>Quantity:</strong> {{ $order->quantity }}<br>
        <strong>Expected Delivery:</strong> {{ $order->expected_delivery_date }}<br>
    </div>
@endforeach