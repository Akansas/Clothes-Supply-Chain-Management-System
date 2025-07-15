<!DOCTYPE html>
<html>
<head>
    <title>Retailer Daily Report</title>
</head>
<body>
    <h2>📦 Retail Store Daily Order Summary - {{ now()->toFormattedDateString() }}</h2>

    <p>Dear {{ $retailer->name }},</p>

    <p>Here are your orders for today:</p>

    @if ($orders->count() > 0)
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Ordered At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->product->name ?? 'N/A' }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->created_at->format('H:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No orders found today.</p>
    @endif

    <p>Regards,<br>Your Supply Chain System</p>
</body>
</html>