@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Delivery Dashboard</h4>
                    <p class="card-category">Welcome back, {{ auth()->user()->name }}!</p>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="icon-big text-center icon-warning">
                                                <i class="nc-icon nc-delivery-fast text-warning"></i>
                        </div>
                    </div>
                                        <div class="col-7">
                                            <div class="numbers">
                                                <p class="card-category">Total Deliveries</p>
                                                <h4 class="card-title">{{ $stats['total_deliveries'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-clock-o"></i> {{ $stats['pending_deliveries'] }} Pending
                        </div>
                    </div>
                </div>
            </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="icon-big text-center icon-success">
                                                <i class="nc-icon nc-check-2 text-success"></i>
                        </div>
                    </div>
                                        <div class="col-7">
                                            <div class="numbers">
                                                <p class="card-category">Completed</p>
                                                <h4 class="card-title">{{ $stats['completed_deliveries'] }}</h4>
                </div>
            </div>
        </div>
    </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-calendar"></i> Today: {{ $stats['today_deliveries'] }}
                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-body">
                            <div class="row">
                                        <div class="col-5">
                                            <div class="icon-big text-center icon-info">
                                                <i class="nc-icon nc-chart-bar-32 text-info"></i>
                                                    </div>
                                                </div>
                                        <div class="col-7">
                                            <div class="numbers">
                                                <p class="card-category">On-Time Rate</p>
                                                <h4 class="card-title">{{ $performance['on_time_rate'] }}%</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-clock-o"></i> Avg: {{ round($performance['avg_delivery_time'], 1) }} days
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="icon-big text-center icon-danger">
                                                <i class="nc-icon nc-calendar-60 text-danger"></i>
                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div class="numbers">
                                                <p class="card-category">Monthly</p>
                                                <h4 class="card-title">{{ $stats['monthly_deliveries'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-refresh"></i> This Month
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                    <h5 class="card-title">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('delivery.deliveries') }}" class="btn btn-primary btn-block">
                                                <i class="nc-icon nc-delivery-fast"></i> View All Deliveries
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('delivery.route-optimization') }}" class="btn btn-info btn-block">
                                                <i class="nc-icon nc-chart-pie-35"></i> Route Optimization
                                            </a>
                                                </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('delivery.schedule') }}" class="btn btn-success btn-block">
                                                <i class="nc-icon nc-calendar-60"></i> Today's Schedule
                                            </a>
                                                </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('delivery.map') }}" class="btn btn-warning btn-block">
                                                <i class="nc-icon nc-pin-3"></i> Delivery Map
                                            </a>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Today's Deliveries -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Today's Deliveries</h5>
                                </div>
                                <div class="card-body">
                                    @if($todayDeliveries->count() > 0)
                            <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                        <tr>
                                                        <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Address</th>
                                                        <th>Priority</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                </tr>
            </thead>
            <tbody>
                                                    @foreach($todayDeliveries as $delivery)
                                                    <tr>
                                                        <td>{{ $delivery->order->order_number ?? 'N/A' }}</td>
                                                        <td>{{ $delivery->order->user->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <small class="text-muted">
                                                                {{ Str::limit($delivery->order->shipping_address ?? 'N/A', 30) }}
                                                            </small>
                                                </td>
                                                <td>
                                                            @if($delivery->priority == 'high')
                                                                <span class="badge badge-danger">High</span>
                                                            @elseif($delivery->priority == 'medium')
                                                                <span class="badge badge-warning">Medium</span>
                                                            @else
                                                                <span class="badge badge-info">Low</span>
                                                            @endif
                                                </td>
                                                <td>
                                                            @if($delivery->status == 'pending')
                                                                <span class="badge badge-warning">Pending</span>
                                                            @elseif($delivery->status == 'in_transit')
                                                                <span class="badge badge-info">In Transit</span>
                                                            @elseif($delivery->status == 'out_for_delivery')
                                                                <span class="badge badge-primary">Out for Delivery</span>
                                                            @elseif($delivery->status == 'delivered')
                                                                <span class="badge badge-success">Delivered</span>
                                                            @elseif($delivery->status == 'failed')
                                                                <span class="badge badge-danger">Failed</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ ucfirst($delivery->status) }}</span>
                                                            @endif
                                                </td>
                                                <td>
                                                            <a href="{{ route('delivery.deliveries.show', $delivery->id) }}" class="btn btn-sm btn-primary">View</a>
                                                </td>
                    </tr>
                                                    @endforeach
            </tbody>
        </table>
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ route('delivery.deliveries') }}" class="btn btn-sm btn-info">View All Deliveries</a>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No deliveries scheduled for today</p>
                                    @endif
                                </div>
                            </div>
    </div>

                        <!-- Pending Deliveries -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Pending Deliveries</h5>
                                        </div>
                                        <div class="card-body">
                                    @if($pendingDeliveries->count() > 0)
                                        <div class="list-group">
                                            @foreach($pendingDeliveries->take(5) as $delivery)
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                        <h6 class="mb-1">{{ $delivery->order->user->name ?? 'N/A' }}</h6>
                                                        <small class="text-muted">
                                                            {{ Str::limit($delivery->order->shipping_address ?? 'N/A', 25) }}
                                                        </small>
                                                    </div>
                                                    <span class="badge badge-{{ $delivery->priority == 'high' ? 'danger' : ($delivery->priority == 'medium' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($delivery->priority) }}
                                                    </span>
                                                </div>
                                                <small class="text-muted">
                                                    Est: {{ $delivery->estimated_delivery ? $delivery->estimated_delivery->format('M d, H:i') : 'N/A' }}
                                                </small>
                                            </div>
                                            @endforeach
                                        </div>
                                        @if($pendingDeliveries->count() > 5)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('delivery.deliveries') }}" class="btn btn-sm btn-info">View All ({{ $pendingDeliveries->count() }})</a>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-muted text-center">No pending deliveries</p>
                                    @endif
                                </div>
                                            </div>
                                        </div>
                                    </div>

                    <!-- Recent Completed Deliveries -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Recent Completed Deliveries</h5>
                                </div>
                                <div class="card-body">
                                    @if($recentCompleted->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Order #</th>
                                                        <th>Customer</th>
                                                        <th>Delivery Date</th>
                                                        <th>On Time</th>
                                                        <th>Rating</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentCompleted as $delivery)
                                                    <tr>
                                                        <td>{{ $delivery->order->order_number ?? 'N/A' }}</td>
                                                        <td>{{ $delivery->order->user->name ?? 'N/A' }}</td>
                                                        <td>{{ $delivery->actual_delivery_date ? $delivery->actual_delivery_date->format('M d, Y H:i') : 'N/A' }}</td>
                                                        <td>
                                                            @if($delivery->actual_delivery_date && $delivery->estimated_delivery)
                                                                @if($delivery->actual_delivery_date <= $delivery->estimated_delivery)
                                                                    <span class="badge badge-success">On Time</span>
                                                                @else
                                                                    <span class="badge badge-warning">Late</span>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-secondary">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($delivery->rating)
                                                                <div class="text-warning">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <i class="fa fa-star{{ $i <= $delivery->rating ? '' : '-o' }}"></i>
                                                                    @endfor
                                                                </div>
                                                            @else
                                                                <span class="text-muted">No rating</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No completed deliveries yet</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Performance Overview</h5>
                                        </div>
                                        <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h3 class="text-success">{{ $performance['on_time_rate'] }}%</h3>
                                            <p class="text-muted">On-Time Delivery Rate</p>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-info">{{ round($performance['avg_delivery_time'], 1) }}</h3>
                                            <p class="text-muted">Avg Delivery Time (Days)</p>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Quick Stats</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h3 class="text-warning">{{ $stats['today_deliveries'] }}</h3>
                                            <p class="text-muted">Today's Deliveries</p>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-danger">{{ $stats['pending_deliveries'] }}</h3>
                                            <p class="text-muted">Pending Deliveries</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 