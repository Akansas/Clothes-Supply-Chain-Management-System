@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-3">Manufacturer Analytics Dashboard</h2>
    <p class="text-center text-muted mb-5">Comprehensive insights for your manufacturing operations</p>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-chart-line me-2"></i>Sales & Demand Forecast
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="80"></canvas>
            @if($predicted)
                <div class="alert alert-info mt-3">
                    <strong>Predicted Demand for Tomorrow:</strong> {{ round($predicted) }} units
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-users me-2"></i>Customer Segments
        </div>
        <div class="card-body">
                <div class="row">
                @foreach($segments as $segmentName => $retailers)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                <h5 class="card-title">{{ $segmentName }}</h5>
                                <p class="text-muted small">{{ $segmentDescriptions[$segmentName] ?? '' }}</p>
                                    <ul>
                                    @forelse($retailers as $retailer)
                                        <li>{{ $retailer['name'] }}: Orders: {{ $retailer['orders'] }}</li>
                                    @empty
                                        <li>No retailers in this segment.</li>
                                    @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-database me-2"></i>Raw Sales Orders Used for Analysis
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rawOrders = \App\Models\ProductionOrder::whereNotNull('retailer_id')->where('status', 'delivered')->whereNotNull('created_at')->with('product')->get();
                    @endphp
                    @foreach($rawOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->product->name ?? '-' }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(is_object($orders) && method_exists($orders, 'isNotEmpty') && $orders->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const salesLabels = @json($orders->pluck('date'));
    // Convert string numbers to actual numbers
    const salesData = @json($orders->pluck('total')).map(Number);

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Units Sold',
                    data: salesData,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endif
@endsection 