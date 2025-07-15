@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-center mb-3">Retailer Analytics Dashboard</h2>
            <p class="text-muted text-center">Comprehensive insights for your retail operations</p>
        </div>
    </div>

    {{-- Sales Insights Panel --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Sales Insights</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Sales Performance</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Daily Sales:</strong> 
                            <span class="badge bg-success">${{ number_format($salesInsights['total_sales']['daily'] ?? 0, 2) }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Weekly Sales:</strong> 
                            <span class="badge bg-info">${{ number_format($salesInsights['total_sales']['weekly'] ?? 0, 2) }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Avg. Transaction Value:</strong> 
                            <span class="badge bg-warning">${{ number_format($salesInsights['avg_transaction_value'] ?? 0, 2) }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Channel Performance</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>In-Store:</strong> 
                            <span class="badge bg-secondary">${{ number_format($salesInsights['sales_by_channel']['in_store'] ?? 0, 2) }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Online:</strong> 
                            <span class="badge bg-dark">${{ number_format($salesInsights['sales_by_channel']['online'] ?? 0, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            @if(!empty($salesInsights['top_selling_skus']))
                <div class="mt-3">
                    <h6 class="text-primary">Top Selling Products</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Units Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesInsights['top_selling_skus'] as $sku)
                                    <tr>
                                        <td>{{ $sku['name'] ?? 'Unknown' }}</td>
                                        <td><code>{{ $sku['sku'] ?? 'N/A' }}</code></td>
                                        <td><span class="badge bg-primary">{{ $sku['total_sold'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>No sales data available yet.
                </div>
            @endif
        </div>
    </div>

    {{-- Inventory Intelligence Panel --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Inventory Intelligence</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-info">Inventory Metrics</h6>
                    <p class="mb-2">
                        <strong>Turnover Ratio:</strong> 
                        <span class="badge bg-info">{{ $inventoryIntelligence['inventory_turnover_ratio'] ?? 0 }}</span>
                    </p>
                </div>
            </div>

            @if(!empty($inventoryIntelligence['stock_levels_per_location']))
                <h6 class="text-info">Stock Levels by Location</h6>
                @foreach($inventoryIntelligence['stock_levels_per_location'] as $store)
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <h6 class="mb-2">{{ $store['store_name'] ?? 'Unknown Store' }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($store['products'] ?? [] as $product)
                                            <tr>
                                                <td>{{ $product['name'] ?? 'Unknown' }}</td>
                                                <td><code>{{ $product['sku'] ?? 'N/A' }}</code></td>
                                                <td>
                                                    <span class="badge {{ $product['quantity'] < 10 ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $product['quantity'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            @if(!empty($inventoryIntelligence['aging_stock_report']))
                <h6 class="text-info mt-3">Aging Stock (>90 days)</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Last Restocked</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryIntelligence['aging_stock_report'] as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? 'Unknown' }}</td>
                                    <td><code>{{ $item['sku'] ?? 'N/A' }}</code></td>
                                    <td><span class="badge bg-warning">{{ $item['quantity'] }}</span></td>
                                    <td>{{ $item['last_restocked_at'] ?? 'Unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($inventoryIntelligence['reorder_point_prediction']))
                <h6 class="text-info mt-3">Reorder Alerts</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Current Stock</th>
                                <th>Reorder Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryIntelligence['reorder_point_prediction'] as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? 'Unknown' }}</td>
                                    <td><code>{{ $item['sku'] ?? 'N/A' }}</code></td>
                                    <td><span class="badge bg-danger">{{ $item['quantity'] }}</span></td>
                                    <td>{{ $item['reorder_point'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Customer Behavior & Segmentation --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Customer Behavior & Segmentation</h5>
        </div>
        <div class="card-body">
            @if(!empty($customerBehavior['customer_lifetime_value']))
                <h6 class="text-success">Top Customer Lifetime Value</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Lifetime Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerBehavior['customer_lifetime_value'] as $cust)
                                <tr>
                                    <td>{{ $cust['name'] ?? 'Unknown' }}</td>
                                    <td>{{ $cust['email'] ?? 'N/A' }}</td>
                                    <td><span class="badge bg-success">${{ number_format($cust['lifetime_value'], 2) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($customerBehavior['purchase_frequency']))
                <h6 class="text-success mt-3">Top Purchase Frequency</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Purchase Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerBehavior['purchase_frequency'] as $cust)
                                <tr>
                                    <td>{{ $cust['name'] ?? 'Unknown' }}</td>
                                    <td>{{ $cust['email'] ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ $cust['purchase_count'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($customerBehavior['product_preferences']))
                <h6 class="text-success mt-3">Product Preferences (Top Categories)</h6>
                <ul class="list-group">
                    @foreach($customerBehavior['product_preferences'] as $pref)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $pref['category'] ?? 'Unknown' }}
                            <span class="badge bg-primary rounded-pill">{{ $pref['count'] ?? 0 }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if(!empty($customerBehavior['return_rate_by_segment']))
                <h6 class="text-success mt-3">Return Rate by Customer</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Return Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerBehavior['return_rate_by_segment'] as $cust)
                                <tr>
                                    <td>{{ $cust['name'] ?? 'Unknown' }}</td>
                                    <td>{{ $cust['email'] ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ ($cust['return_rate'] * 100) > 10 ? 'bg-danger' : 'bg-warning' }}">
                                            {{ number_format($cust['return_rate'] * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Pricing & Promotion Effectiveness --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Pricing & Promotion Effectiveness</h5>
        </div>
        <div class="card-body">
            @if(!empty($pricingPromotion['markdown_impact_on_sales']))
                <h6 class="text-warning">Markdown Impact on Sales</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Original Price</th>
                                <th>Sold Price</th>
                                <th>Markdown %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pricingPromotion['markdown_impact_on_sales'] as $item)
                                <tr>
                                    <td><code>{{ $item['sku'] ?? 'N/A' }}</code></td>
                                    <td>${{ number_format($item['original_price'], 2) }}</td>
                                    <td>${{ number_format($item['sold_price'], 2) }}</td>
                                    <td><span class="badge bg-warning">{{ $item['markdown_percent'] }}%</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>Campaign ROI:</strong><br>
                        {{ $pricingPromotion['campaign_roi'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>Elasticity Analysis:</strong><br>
                        {{ $pricingPromotion['elasticity_analysis'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>Seasonal Performance:</strong><br>
                        {{ $pricingPromotion['seasonal_performance'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Omnichannel Engagement --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Omnichannel Engagement</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-secondary">
                        <strong>Cart Abandonment Rate:</strong><br>
                        {{ $omnichannelEngagement['cart_abandonment_rate'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-secondary">
                        <strong>Store Foot Traffic:</strong><br>
                        {{ $omnichannelEngagement['store_foot_traffic'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-secondary">
                        <strong>Social Media Mentions:</strong><br>
                        {{ $omnichannelEngagement['social_media_mentions'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-secondary">Return Rate by Channel</h6>
                    <ul class="list-unstyled">
                        <li><strong>Online:</strong> <span class="badge bg-secondary">{{ $omnichannelEngagement['return_rate_online_vs_store']['online'] ?? 0 }}</span></li>
                        <li><strong>Store:</strong> <span class="badge bg-secondary">{{ $omnichannelEngagement['return_rate_online_vs_store']['store'] ?? 0 }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Actionable Alerts & Recommendations --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Actionable Alerts & Recommendations</h5>
        </div>
        <div class="card-body">
            @if(!empty($actionableAlerts['low_stock_alerts']))
                <h6 class="text-danger">Low Stock Alerts</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Current Stock</th>
                                <th>Reorder Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($actionableAlerts['low_stock_alerts'] as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? 'Unknown' }}</td>
                                    <td><code>{{ $item['sku'] ?? 'N/A' }}</code></td>
                                    <td><span class="badge bg-danger">{{ $item['quantity'] }}</span></td>
                                    <td>{{ $item['reorder_point'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($actionableAlerts['product_bundling_suggestions']))
                <h6 class="text-danger mt-3">Product Bundling Suggestions</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Units Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($actionableAlerts['product_bundling_suggestions'] as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? 'Unknown' }}</td>
                                    <td><code>{{ $item['sku'] ?? 'N/A' }}</code></td>
                                    <td><span class="badge bg-primary">{{ $item['total_sold'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <strong>New Trend Alerts:</strong><br>
                        {{ $actionableAlerts['new_trend_alerts'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>Reorder Automation Triggers:</strong><br>
                        @if(isset($actionableAlerts['reorder_automation_triggers']))
                            {{ count($actionableAlerts['reorder_automation_triggers']) }} items
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Market Trend Insights --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Market Trend Insights</h5>
        </div>
        <div class="card-body">
            @if(!empty($marketTrends['trending_products']))
                <h6 class="text-dark">Trending Products (Last 30 Days)</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Units Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marketTrends['trending_products'] as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? 'Unknown' }}</td>
                                    <td><code>{{ $item['sku'] ?? 'N/A' }}</code></td>
                                    <td><span class="badge bg-dark">{{ $item['total_sold'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="alert alert-dark">
                        <strong>Market Growth Rate:</strong><br>
                        @if($marketTrends['market_growth_rate'] !== null)
                            <span class="badge {{ $marketTrends['market_growth_rate'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $marketTrends['market_growth_rate'] }}%
                            </span>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-dark">
                        <strong>Seasonal Trends:</strong><br>
                        {{ $marketTrends['seasonal_trends'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-dark">
                        <strong>Competitive Benchmarking:</strong><br>
                        {{ $marketTrends['competitive_benchmarking'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    border-bottom: none;
}

.badge {
    font-size: 0.8em;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
}

.alert {
    border-radius: 8px;
    border: none;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.9em;
}
</style>
@endsection 