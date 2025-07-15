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
            <div class="card shadow-lg border-0">
                <div class="card-header bg-info text-white d-flex align-items-center">
                    <i class="fas fa-chart-bar fa-lg me-2"></i>
                    <h5 class="mb-0">Retailer Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if(isset($salesInsights))
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-chart-line fa-2x text-primary me-2"></i>
                                        <h6 class="mb-0">Sales Insights</h6>
                                    </div>
                                    <div class="text-muted small mb-2">Sales performance and trends.</div>
                                    <div>
                                        @if(is_array($salesInsights))
                                            <ul class="list-group list-group-flush">
                                                @foreach($salesInsights as $k => $v)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}</span>
                                                        <span class="fw-bold">{{ is_numeric($v) ? number_format($v) : $v }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="fw-bold">{{ $salesInsights }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($inventoryIntelligence))
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-boxes fa-2x text-success me-2"></i>
                                        <h6 class="mb-0">Inventory Intelligence</h6>
                                    </div>
                                    <div class="text-muted small mb-2">Stock levels and restock alerts.</div>
                                    <div>
                                        @if(is_array($inventoryIntelligence))
                                            <ul class="list-group list-group-flush">
                                                @foreach($inventoryIntelligence as $k => $v)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}</span>
                                                        <span class="fw-bold">{{ is_numeric($v) ? number_format($v) : $v }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="fw-bold">{{ $inventoryIntelligence }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($customerBehavior))
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-users fa-2x text-info me-2"></i>
                                        <h6 class="mb-0">Customer Behavior</h6>
                                    </div>
                                    <div class="text-muted small mb-2">Customer preferences and patterns.</div>
                                    <div>
                                        @if(is_array($customerBehavior))
                                            <ul class="list-group list-group-flush">
                                                @foreach($customerBehavior as $k => $v)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}</span>
                                                        <span class="fw-bold">{{ is_numeric($v) ? number_format($v) : $v }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="fw-bold">{{ $customerBehavior }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($pricingPromotion))
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-tags fa-2x text-warning me-2"></i>
                                        <h6 class="mb-0">Pricing & Promotion</h6>
                                    </div>
                                    <div class="text-muted small mb-2">Discounts and pricing strategies.</div>
                                    <div>
                                        @if(is_array($pricingPromotion))
                                            <ul class="list-group list-group-flush">
                                                @foreach($pricingPromotion as $k => $v)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}</span>
                                                        <span class="fw-bold">{{ is_numeric($v) ? number_format($v) : $v }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="fw-bold">{{ $pricingPromotion }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($omnichannelEngagement))
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-network-wired fa-2x text-secondary me-2"></i>
                                        <h6 class="mb-0">Omnichannel Engagement</h6>
                                    </div>
                                    <div class="text-muted small mb-2">Multi-channel sales and engagement.</div>
                                    <div>
                                        @if(is_array($omnichannelEngagement))
                                            <ul class="list-group list-group-flush">
                                                @foreach($omnichannelEngagement as $k => $v)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}</span>
                                                        <span class="fw-bold">{{ is_numeric($v) ? number_format($v) : $v }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="fw-bold">{{ $omnichannelEngagement }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($actionableAlerts))
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-bell fa-2x text-danger me-2"></i>
                                        <h6 class="mb-0">Actionable Alerts</h6>
                                    </div>
                                    <div class="text-muted small mb-2">Critical issues and alerts.</div>
                                    <div>
                                        @if(is_array($actionableAlerts))
                                            <ul class="list-group list-group-flush">
                                                @foreach($actionableAlerts as $k => $v)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}</span>
                                                        <span class="fw-bold">{{ is_numeric($v) ? number_format($v) : $v }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="fw-bold">{{ $actionableAlerts }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($marketTrends))
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-chart-area fa-2x text-info me-2"></i>
                                        <h6 class="mb-0">Market Trends</h6>
                                    </div>
                                    <div class="text-muted small mb-2">Industry and market trends.</div>
                                    <div>
                                        @if(is_array($marketTrends))
                                            <ul class="list-group list-group-flush">
                                                @foreach($marketTrends as $k => $v)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}</span>
                                                        <span class="fw-bold">{{ is_numeric($v) ? number_format($v) : $v }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="fw-bold">{{ $marketTrends }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
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