<h2>Daily Production Orders Report</h2>

@foreach($orders as $order)
    <p>
        <strong>Order ID:</strong> {{ $order->id }}<br>
        <strong>Product:</strong> {{ $order->product->name ?? 'N/A' }}<br>
        <strong>Quantity:</strong> {{ $order->quantity }}<br>
        <strong>Status:</strong> {{ $order->status }}<br>
        <strong>Requested At:</strong> {{ $order->start_date ? $order->start_date->format('Y-m-d'): 'N/A' }}
    </p>
    <hr>
@endforeach