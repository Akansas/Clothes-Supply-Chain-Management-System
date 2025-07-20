<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supplier Report - {{ $supplier->name ?? $user->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .section { margin-bottom: 20px; }
        .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Supplier Daily Report</h1>
        <p>Generated on: {{ $reportDate }}</p>
        <p>Supplier: {{ $supplier->name ?? $user->name }}</p>
    </div>

    <div class="section">
        <h3>Inventory Summary</h3>
        <div class="summary">
            <p><strong>Total Materials:</strong> {{ $stats['total_materials'] }}</p>
            <p><strong>Total Revenue:</strong> ${{ number_format($stats['total_revenue'], 2) }}</p>
            <p><strong>Inventory Value:</strong> ${{ number_format($stats['total_cost'], 2) }}</p>
            <p><strong>Monthly Orders:</strong> {{ $stats['monthly_orders'] }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Order Status Summary</h3>
        <div class="summary">
            <p><strong>Active Orders:</strong> {{ $stats['active_orders'] }}</p>
            <p><strong>Completed Deliveries:</strong> {{ $stats['completed_deliveries'] }}</p>
            <p><strong>Approved Orders:</strong> {{ $stats['approved_orders'] }}</p>
            <p><strong>Cancelled Orders:</strong> {{ $stats['cancelled_orders'] }}</p>
            <p><strong>Rejected Orders:</strong> {{ $stats['rejected_orders'] }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Pending Orders</h3>
        @if($pendingOrders->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No pending orders found.</p>
        @endif
    </div>

    <div class="section">
        <h3>Completed Orders</h3>
        @if($completedOrders->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedOrders->take(10) as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No completed orders found.</p>
        @endif
    </div>

    <div class="section">
        <h3>Material Inventory</h3>
        @if($inventory->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory->take(10) as $material)
                        <tr>
                            <td>{{ $material->name }}</td>
                            <td>${{ number_format($material->price, 2) }}</td>
                            <td>{{ $material->stock_quantity ?? 0 }}</td>
                            <td>{{ $material->unit ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No materials found.</p>
        @endif
    </div>

    <div class="footer">
        <p>This report was automatically generated by the Supply Chain Management System.</p>
        <p>For questions, please contact the system administrator.</p>
    </div>
</body>
</html> 