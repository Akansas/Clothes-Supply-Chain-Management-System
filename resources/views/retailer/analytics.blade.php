@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Retailer Analytics Dashboard</h1>
            <p class="text-muted">Comprehensive insights for {{ $retailStore->name }}</p>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <!-- Total Orders -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-shopping-cart me-2"></i>Total Orders
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $orderAnalytics['total_orders'] }}</div>
                            <div class="text-xs text-muted mt-1">This month: {{ $orderAnalytics['monthly_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-dollar-sign me-2"></i>Total Cost
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">${{ number_format($orderAnalytics['total_revenue'], 2) }}</div>
                            <div class="text-xs text-muted mt-1">Avg: ${{ number_format($orderAnalytics['avg_order_value'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-dollar-sign fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Products -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-boxes me-2"></i>Inventory Products
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $inventoryAnalytics['total_products'] }}</div>
                            <div class="text-xs text-muted mt-1">Low stock: {{ $inventoryAnalytics['low_stock_products'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-boxes fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Score -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-chart-line me-2"></i>Performance
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $performanceMetrics['order_completion_rate'] }}%</div>
                            <div class="text-xs text-muted mt-1">Order completion rate</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-chart-line fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <!-- Order Status Pie Chart -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Order Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div id="orderStatusDebug" class="mt-2 text-muted small"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Cost Trend -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-line me-2"></i>Monthly Cost Trend
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div id="revenueDebug" class="mt-2 text-muted small"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <!-- Inventory Stock Levels -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-boxes me-2"></i>Inventory Stock Levels
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                    <div id="inventoryDebug" class="mt-2 text-muted small"></div>
                </div>
            </div>
        </div>

        <!-- Order Volume Trend -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-bar me-2"></i>Order Volume Trend
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="orderVolumeChart"></canvas>
                    </div>
                    <div id="orderVolumeDebug" class="mt-2 text-muted small"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Dashboard -->
    <div class="row mb-4">
        <!-- Performance Metrics -->
        <div class="col-xl-8 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-bar me-2"></i>Performance Metrics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-4 border rounded">
                                <div class="display-4 text-success mb-2">{{ $performanceMetrics['order_completion_rate'] }}%</div>
                                <h6 class="text-muted">Order Completion Rate</h6>
                                <small class="text-muted">Successfully delivered orders</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-4 border rounded">
                                <div class="display-4 text-warning mb-2">{{ $performanceMetrics['customer_satisfaction'] }}%</div>
                                <h6 class="text-muted">Customer Satisfaction</h6>
                                <small class="text-muted">Based on feedback</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-xl-4 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts
                    </h6>
                </div>
                <div class="card-body">
                    @if($inventoryAnalytics['low_stock_items']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventoryAnalytics['low_stock_items']->take(5) as $item)
                                        <tr>
                                            <td>{{ Str::limit($item->product->name ?? 'Unknown', 15) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $item->quantity == 0 ? 'danger' : 'warning' }}">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('retailer.inventory') }}" class="btn btn-sm btn-outline-primary">
                                                    Restock
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-success">All products well stocked!</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN with fallback -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
// Fallback if Chart.js fails to load
setTimeout(function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js failed to load, using fallback');
        document.querySelectorAll('canvas').forEach(function(canvas) {
            const ctx = canvas.getContext('2d');
            ctx.font = '16px Arial';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            ctx.fillText('Chart loading...', canvas.width / 2, canvas.height / 2);
        });
    }
}, 3000);
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing charts...');
    
    // Check if Chart.js loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded!');
        return;
    }
    
    console.log('Chart.js loaded successfully');
    
    // Chart colors
    const colors = {
        primary: '#4e73df',
        success: '#1cc88a',
        warning: '#f6c23e',
        danger: '#e74a3b',
        info: '#36b9cc',
        secondary: '#858796'
    };
    
    // Get data from PHP
    let orderAnalytics = @json($orderAnalytics);
    let inventoryAnalytics = @json($inventoryAnalytics);
    let monthlyTrends = @json($monthlyTrends);
    
    console.log('Data received:', { orderAnalytics, inventoryAnalytics, monthlyTrends });
    
    // Add fallback data if no data is available
    if (!orderAnalytics || Object.keys(orderAnalytics).length === 0) {
        console.warn('No order analytics data, using fallback');
        orderAnalytics = {
            total_orders: 0,
            orders_by_status: { 'pending': 0, 'delivered': 0, 'cancelled': 0 },
            monthly_orders: 0,
            total_revenue: 0,
            avg_order_value: 0
        };
    }
    
    if (!inventoryAnalytics || Object.keys(inventoryAnalytics).length === 0) {
        console.warn('No inventory analytics data, using fallback');
        inventoryAnalytics = {
            total_products: 0,
            low_stock_products: 0,
            out_of_stock: 0,
            low_stock_items: []
        };
    }
    
    if (!monthlyTrends || Object.keys(monthlyTrends).length === 0) {
        console.warn('No monthly trends data, using fallback');
        monthlyTrends = {};
    }
    
    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        try {
            const orderStatusData = orderAnalytics.orders_by_status || {};
            const labels = Object.keys(orderStatusData).map(status => status.charAt(0).toUpperCase() + status.slice(1));
            const values = Object.values(orderStatusData);
            
            console.log('Order status data:', { labels, values });
            
            if (values.length > 0 && values.some(val => val > 0)) {
                const orderStatusChart = new Chart(orderStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: [colors.success, colors.warning, colors.danger, colors.info, colors.secondary],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
                console.log('Order status chart created successfully');
            } else {
                document.getElementById('orderStatusDebug').innerHTML = 'No order data available';
                console.log('No order data available for chart');
            }
        } catch (error) {
            console.error('Error creating order status chart:', error);
            document.getElementById('orderStatusDebug').innerHTML = 'Error creating chart: ' + error.message;
        }
    } else {
        console.error('Order status chart canvas not found');
    }
    
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        try {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const revenueData = months.map((month, index) => {
                const monthData = monthlyTrends[index + 1];
                return monthData ? parseFloat(monthData.revenue) : 0;
            });
            
            console.log('Cost data:', revenueData);
            
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Cost ($)',
                        data: revenueData,
                        borderColor: colors.success,
                        backgroundColor: colors.success + '20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Revenue chart created successfully');
        } catch (error) {
            console.error('Error creating revenue chart:', error);
            document.getElementById('revenueDebug').innerHTML = 'Error creating chart: ' + error.message;
        }
    } else {
        console.error('Revenue chart canvas not found');
    }
    
    // Inventory Chart
    const inventoryCtx = document.getElementById('inventoryChart');
    if (inventoryCtx) {
        try {
            const inventoryData = {
                'Well Stocked': inventoryAnalytics.total_products - inventoryAnalytics.low_stock_products,
                'Low Stock': inventoryAnalytics.low_stock_products
            };
            
            console.log('Inventory data:', inventoryData);
            
            const inventoryChart = new Chart(inventoryCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(inventoryData),
                    datasets: [{
                        label: 'Products',
                        data: Object.values(inventoryData),
                        backgroundColor: [colors.success, colors.warning],
                        borderWidth: 0,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Inventory chart created successfully');
        } catch (error) {
            console.error('Error creating inventory chart:', error);
            document.getElementById('inventoryDebug').innerHTML = 'Error creating chart: ' + error.message;
        }
    } else {
        console.error('Inventory chart canvas not found');
    }
    
    // Order Volume Chart
    const orderVolumeCtx = document.getElementById('orderVolumeChart');
    if (orderVolumeCtx) {
        try {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const orderVolumeData = months.map((month, index) => {
                const monthData = monthlyTrends[index + 1];
                return monthData ? parseInt(monthData.count) : 0;
            });
            
            console.log('Order volume data:', orderVolumeData);
            
            const orderVolumeChart = new Chart(orderVolumeCtx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Orders',
                        data: orderVolumeData,
                        backgroundColor: colors.info + '80',
                        borderColor: colors.info,
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Order volume chart created successfully');
        } catch (error) {
            console.error('Error creating order volume chart:', error);
            document.getElementById('orderVolumeDebug').innerHTML = 'Error creating chart: ' + error.message;
        }
    } else {
        console.error('Order volume chart canvas not found');
    }
});
</script>

<style>
/* Enhanced Card Styling */
.card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
}

.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }

/* Icon Background Styling */
.bg-opacity-10 { opacity: 0.1; }
.rounded-circle { border-radius: 50% !important; }
.p-3 { padding: 1rem !important; }

/* Text Styling */
.text-primary { color: #4e73df !important; }
.text-success { color: #1cc88a !important; }
.text-warning { color: #f6c23e !important; }
.text-info { color: #36b9cc !important; }
.text-danger { color: #e74a3b !important; }

/* Card Body Enhancement */
.card-body { padding: 1.5rem; }

/* Chart container styling - Fixed height and proper positioning */
.chart-container {
    position: relative;
    height: 300px !important;
    width: 100% !important;
    margin: 0 auto;
}

/* Ensure charts are properly sized */
.chart-container canvas {
    max-height: 300px !important;
    width: 100% !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body { padding: 1rem; }
    .h2 { font-size: 1.5rem; }
    .display-4 { font-size: 2rem; }
    .chart-container {
        height: 250px !important;
    }
}

/* Animation for icons */
.fa-2x {
    transition: transform 0.3s ease;
}

.card:hover .fa-2x {
    transform: scale(1.1);
}

/* Table styling */
.table-sm th, .table-sm td {
    padding: 0.5rem;
    vertical-align: middle;
}

/* Badge styling */
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* Fix for chart rendering issues */
canvas {
    display: block !important;
    max-width: 100% !important;
}

/* Ensure proper chart sizing */
.chart-container {
    overflow: hidden;
}

/* Debug info styling */
#orderStatusDebug, #revenueDebug, #inventoryDebug, #orderVolumeDebug {
    font-size: 0.875rem;
    color: #6c757d;
    text-align: center;
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    margin-top: 0.5rem;
}
</style>
@endsection 