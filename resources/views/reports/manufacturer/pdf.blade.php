<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manufacturer Daily Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 30px;
        }
        .info p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 16px;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .profit-margin {
            font-weight: bold;
            color: #dc3545;
        }
        .profit-positive {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manufacturer Daily Report</h1>
        <p><strong>Generated on:</strong> {{ $reportDate }}</p>
        <p><strong>Manufacturer:</strong> {{ $manufacturer->name }}</p>
    </div>

    <div class="section">
        <h2>Production Statistics Summary</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Financial Overview</h3>
                <div class="stat-row">
                    <span>Total Revenue:</span>
                    <span>${{ number_format($totalRevenue, 2) }}</span>
                </div>
                <div class="stat-row">
                    <span>Total Cost:</span>
                    <span>${{ number_format($totalCost, 2) }}</span>
                </div>
                <div class="stat-row">
                    <span>Profit Margin:</span>
                    <span class="profit-margin {{ $totalRevenue > 0 && ($totalRevenue - $totalCost) > 0 ? 'profit-positive' : '' }}">
                        {{ $totalRevenue > 0 ? number_format((($totalRevenue - $totalCost) / $totalRevenue) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
            <div class="stat-card">
                <h3>Order Summary</h3>
                <div class="stat-row">
                    <span>Total Purchase Orders:</span>
                    <span>{{ array_sum($purchaseOrdersStats) }}</span>
                </div>
                <div class="stat-row">
                    <span>Total Retailer Orders:</span>
                    <span>{{ array_sum($retailerOrdersStats) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Order Status Summary</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Purchase Orders Status (From Suppliers)</h3>
                <div class="stat-row">
                    <span>Pending Orders:</span>
                    <span>{{ $purchaseOrdersStats['pending'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Approved Orders:</span>
                    <span>{{ $purchaseOrdersStats['approved'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Delivered Orders:</span>
                    <span>{{ $purchaseOrdersStats['delivered'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Cancelled Orders:</span>
                    <span>{{ $purchaseOrdersStats['cancelled'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Rejected Orders:</span>
                    <span>{{ $purchaseOrdersStats['rejected'] ?? 0 }}</span>
                </div>
            </div>
            <div class="stat-card">
                <h3>Retailer Orders Status (To Retailers)</h3>
                <div class="stat-row">
                    <span>Pending Orders:</span>
                    <span>{{ $retailerOrdersStats['pending'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Approved Orders:</span>
                    <span>{{ $retailerOrdersStats['approved'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Delivered Orders:</span>
                    <span>{{ $retailerOrdersStats['delivered'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Cancelled Orders:</span>
                    <span>{{ $retailerOrdersStats['cancelled'] ?? 0 }}</span>
                </div>
                <div class="stat-row">
                    <span>Rejected Orders:</span>
                    <span>{{ $retailerOrdersStats['rejected'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($recentPurchaseOrders->count() > 0)
    <div class="section">
        <h2>Recent Purchase Orders</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPurchaseOrders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($recentRetailerOrders->count() > 0)
    <div class="section">
        <h2>Recent Retailer Orders</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentRetailerOrders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>${{ $order->product ? number_format($order->product->price * $order->quantity, 2) : '0.00' }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by the Supply Chain Management System.</p>
        <p>For questions, please contact the system administrator.</p>
    </div>
</body>
</html> 