<!DOCTYPE html>
<html>
<head>
    <title>Monthly Supplier Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }

        h2 {
            text-align: center;
        }

        .supplier-info {
            margin-bottom: 20px;
        }

        .order {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .order strong {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <h2>Monthly Purchase Orders</h2>

    <div class="supplier-info">
        <p><strong>Supplier Name:</strong> {{ $supplier->name }}</p>
        <p><strong>Email:</strong> {{ $supplier->email }}</p>
        
    </div>

    @if($orders->count())
       @foreach ($orders as $order)
    <div class="order">
        <p><strong>Manufacturer:</strong> {{ $order->manufacturer->user->name ?? 'N/A' }}</p>
        <p><strong>Ordered On:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>

        <hr>
        @foreach ($order->orderItems as $item)
            <p><strong>Product:</strong> {{ $item->product->name ?? 'N/A' }}</p>
            <p><strong>Quantity:</strong> {{ $item->quantity }}</p>
        @endforeach
    </div>
@endforeach
    @else
        <p>No orders were placed  {{ $month }}.</p>
    @endif
</body>
</html>