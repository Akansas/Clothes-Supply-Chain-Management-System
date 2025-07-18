<!DOCTYPE html>
<html>
<head>
    <title>Manufacturer Monthly Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .manufacturer-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        .no-data {
            text-align: center;
            margin-top: 30px;
            color: red;
        }

        .summary {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Monthly Manufacturer Report</h2>

    <div class="manufacturer-info">
        <p><strong>Manufacturer:</strong> {{ $manufacturer->name ?? 'N/A' }}</p>
        <p><strong>Month:</strong> {{ $month }}</p>
    </div>

    @if(isset($productionOrders) && $productionOrders->count())
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Requested At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productionOrders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->product->name ?? 'N/A' }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->start_date)->format('Y-m-d') ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="summary">Total Orders This Month: {{ $productionOrders->count() }}</p>
    @else
        <p class="no-data">No production orders were placed during {{ $month}}.</p>
    @endif
</body>
</html>