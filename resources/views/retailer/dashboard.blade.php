@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Retail Store Dashboard</h4>
                    <p class="card-category">Welcome back, {{ auth()->user()->name }}</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="icon-big text-center icon-warning">
                                                <i class="nc-icon nc-chart text-warning"></i>
                        </div>
                    </div>
                                        <div class="col-7">
                                            <div class="numbers">
                                                <p class="card-category">Total Orders</p>
                                                <h4 class="card-title">{{ $stats['total_orders'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-clock-o"></i> All time orders
                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-body">
                            <div class="row">
                                        <div class="col-5">
                                            <div class="icon-big text-center icon-info">
                                                <i class="nc-icon nc-chart-pie-35 text-info"></i>
                                            </div>
                                                </div>
                                        <div class="col-7">
                                            <div class="numbers">
                                                <p class="card-category">Pending Orders</p>
                                                <h4 class="card-title">{{ $stats['pending_orders'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-refresh"></i> Need attention
                                            </div>
                                                </div>
                                                </div>
                                                </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="icon-big text-center icon-success">
                                                <i class="nc-icon nc-money-coins text-success"></i>
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div class="numbers">
                                                <p class="card-category">Total Cost</p>
                                                <h4 class="card-title">${{ number_format($stats['total_cost'], 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-calendar-o"></i> From all orders
                                            </div>
                                                </div>
                                                </div>
                                                </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Recent Orders</h4>
                                    <p class="card-category">Latest orders</p>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentOrders as $order)
                                                    <tr>
                                                        <td>{{ $order->order_number }}</td>
                                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $order->getStatusBadgeClass() }}">
                                                                {{ $order->getStatusText() }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('retailer.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No recent orders</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <a href="{{ route('retailer.orders') }}" class="text-decoration-none">
                                            <i class="fa fa-eye"></i> View All Orders
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Quick Actions</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('retailer.production-orders.index') }}" class="btn btn-primary btn-block">
                                                <i class="fa fa-shopping-cart"></i> Manage Orders
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('retailer.inventory') }}" class="btn btn-info btn-block">
                                                <i class="fa fa-box"></i> Inventory Management
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('retailer.analytics') }}" class="btn btn-success btn-block">
                                                <i class="fa fa-chart-bar"></i> View Analytics
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('retailer.products.browse') }}" class="btn btn-outline-primary w-100">Browse Manufacturer Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Analytics Section -->
    @if(isset($salesInsights) || isset($inventoryIntelligence) || isset($customerBehavior) || isset($pricingPromotion) || isset($omnichannelEngagement) || isset($actionableAlerts) || isset($marketTrends))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Retailer Analytics</h5>
                </div>
                <div class="card-body">
                    @if(isset($salesInsights))
                        <h6>Sales Insights</h6>
                        <pre>{{ json_encode($salesInsights, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    @if(isset($inventoryIntelligence))
                        <h6>Inventory Intelligence</h6>
                        <pre>{{ json_encode($inventoryIntelligence, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    @if(isset($customerBehavior))
                        <h6>Customer Behavior</h6>
                        <pre>{{ json_encode($customerBehavior, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    @if(isset($pricingPromotion))
                        <h6>Pricing & Promotion</h6>
                        <pre>{{ json_encode($pricingPromotion, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    @if(isset($omnichannelEngagement))
                        <h6>Omnichannel Engagement</h6>
                        <pre>{{ json_encode($omnichannelEngagement, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    @if(isset($actionableAlerts))
                        <h6>Actionable Alerts</h6>
                        <pre>{{ json_encode($actionableAlerts, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    @if(isset($marketTrends))
                        <h6>Market Trends</h6>
                        <pre>{{ json_encode($marketTrends, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row mb-4">
        <div class="col-12">
            <retailer-chat :user-id="{{ auth()->id() }}"></retailer-chat>
        </div>
    </div>
</div>
@endsection 