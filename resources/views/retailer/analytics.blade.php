@extends('layouts.app', ['activePage' => 'retailer_analytics', 'title' => 'Sales Analytics', 'navName' => 'Sales Analytics', 'activeButton' => 'retailer'])

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center icon-success">
                                <i class="nc-icon nc-money-coins text-success"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Total Revenue</p>
                                <p class="card-title">${{ number_format($totalRevenue, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center icon-info">
                                <i class="nc-icon nc-basket text-info"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Total Orders</p>
                                <p class="card-title">{{ $totalOrders }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center icon-warning">
                                <i class="nc-icon nc-chart-bar-32 text-warning"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Avg. Order Value</p>
                                <p class="card-title">${{ number_format($averageOrderValue, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Revenue Over Time</h4>
                </div>
                <div class="card-body">
                    <canvas id="salesOverTimeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Best-Selling Products</h4>
                </div>
                <div class="card-body">
                    <canvas id="bestSellingChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Product Performance</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Total Sales</th>
                                    <th>Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productPerformance as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->orders->sum('quantity') }}</td>
                                    <td>${{ number_format($product->orders->sum('total'), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Over Time Chart
    const salesOverTimeCtx = document.getElementById('salesOverTimeChart').getContext('2d');
    const salesOverTimeChart = new Chart(salesOverTimeCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesOverTime->pluck('date')) !!},
            datasets: [{
                label: 'Total Revenue',
                data: {!! json_encode($salesOverTime->pluck('total_revenue')) !!},
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Revenue Over Time' }
            }
        }
    });

    // Best-Selling Products Chart
    const bestSellingCtx = document.getElementById('bestSellingChart').getContext('2d');
    const bestSellingChart = new Chart(bestSellingCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($bestSellingLabels) !!},
            datasets: [{
                label: 'Total Sales',
                data: {!! json_encode($bestSellingData) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Best-Selling Products' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush 