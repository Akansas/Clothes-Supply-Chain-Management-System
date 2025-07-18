<!DOCTYPE html>
<html>
<head>
    <title>Retailer Monthly Report</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
    </style>
</head>
<body>
    @php
        // Fallback if $retailer is named $store
        if (!isset($retailer) && isset($store)) {
            $retailer = $store;
        }

        // Month fallback
        $reportMonth = $month ?? \Carbon\Carbon::now()->format('F Y');
    @endphp

    <h2>Retailer Report for {{ $retailer->name ?? 'Retailer' }} — {{ $reportMonth }}</h2>

    @if ($orders->isEmpty())
        <p>No orders found for {{ $reportMonth }}.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    @foreach ($order->orderItems as $item)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>