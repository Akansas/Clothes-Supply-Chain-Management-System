@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-center mb-3">Supplier Analytics Dashboard</h2>
            <p class="text-muted text-center">Comprehensive insights for your supply chain operations</p>
        </div>
    </div>

    {{-- Demand Forecasting --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Demand Forecasting</h5>
        </div>
        <div class="card-body">
            @if(!empty($demandForecasting['orders_by_month']))
                <h6 class="text-primary">Orders by Month</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Order Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandForecasting['orders_by_month'] as $row)
                                <tr>
                                    <td>{{ $row['year'] ?? '' }}-{{ str_pad($row['month'] ?? '', 2, '0', STR_PAD_LEFT) }}</td>
                                    <td><span class="badge bg-primary">{{ $row['order_count'] ?? 0 }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($demandForecasting['orders_by_category']))
                <h6 class="text-primary mt-3">Orders by Category</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Order Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandForecasting['orders_by_category'] as $row)
                                <tr>
                                    <td>{{ $row['category'] ?? 'Unknown' }}</td>
                                    <td><span class="badge bg-info">{{ $row['order_count'] ?? 0 }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($demandForecasting['orders_by_region']))
                <h6 class="text-primary mt-3">Orders by Region</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Region</th>
                                <th>Order Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandForecasting['orders_by_region'] as $row)
                                <tr>
                                    <td>{{ $row['shipping_state'] ?? 'Unknown' }}</td>
                                    <td><span class="badge bg-secondary">{{ $row['order_count'] ?? 0 }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="alert alert-info mt-3">
                <strong>Predicted Demand:</strong> {{ $demandForecasting['predicted_demand'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Lead Time & Order Fulfillment Tracking --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Lead Time & Order Fulfillment Tracking</h5>
        </div>
        <div class="card-body">
            @if(!empty($leadTimeTracking['orders']))
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Status</th>
                                <th>Lead Time (days)</th>
                                <th>Delay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leadTimeTracking['orders'] as $order)
                                <tr>
                                    <td><code>{{ $order['order_number'] }}</code></td>
                                    <td>
                                        <span class="badge {{ $order['status'] === 'delivered' ? 'bg-success' : ($order['status'] === 'delayed' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order['lead_time_days'] !== null)
                                            <span class="badge bg-info">{{ $order['lead_time_days'] }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $order['delay'] ? 'bg-danger' : 'bg-success' }}">
                                            {{ $order['delay'] ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No order data available yet.
                </div>
            @endif

            <div class="alert alert-warning mt-3">
                <strong>Delay Causes:</strong> {{ $leadTimeTracking['delay_causes'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Material Cost & Price Analytics --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Material Cost & Price Analytics</h5>
        </div>
        <div class="card-body">
            @if(!empty($materialCostAnalytics['material_costs']))
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Region</th>
                                <th>Cost</th>
                                <th>Vendor</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialCostAnalytics['material_costs'] as $mat)
                                <tr>
                                    <td>{{ $mat['name'] ?? '' }}</td>
                                    <td><span class="badge bg-secondary">{{ $mat['type'] ?? '' }}</span></td>
                                    <td>{{ $mat['region'] ?? '' }}</td>
                                    <td><span class="badge bg-success">${{ number_format($mat['cost'] ?? 0, 2) }}</span></td>
                                    <td>{{ $mat['vendor_id'] ?? '' }}</td>
                                    <td>{{ $mat['created_at'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No material cost data available yet.
                </div>
            @endif

            <div class="alert alert-info mt-3">
                <strong>Margin Impact:</strong> {{ $materialCostAnalytics['margin_impact'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Quality Control Analysis --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Quality Control Analysis</h5>
        </div>
        <div class="card-body">
            @if(!empty($qualityControlAnalysis['defect_rates']))
                <h6 class="text-warning">Defect Rates by Cause</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Defect Category</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($qualityControlAnalysis['defect_rates'] as $row)
                                <tr>
                                    <td>{{ $row['reason_for_rejection'] ?? 'Unknown' }}</td>
                                    <td><span class="badge bg-warning">{{ $row['count'] ?? 0 }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <strong>Return Frequencies:</strong><br>
                        <span class="badge bg-warning">{{ $qualityControlAnalysis['return_frequencies'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>Compliance:</strong><br>
                        {{ $qualityControlAnalysis['compliance'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Client Satisfaction & Relationship Metrics --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Client Satisfaction & Relationship Metrics</h5>
        </div>
        <div class="card-body">
            @if(!empty($clientSatisfaction['repeat_orders']))
                <h6 class="text-secondary">Repeat Orders (Top Clients)</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Client ID</th>
                                <th>Order Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientSatisfaction['repeat_orders'] as $row)
                                <tr>
                                    <td>Client #{{ $row['user_id'] ?? '' }}</td>
                                    <td><span class="badge bg-secondary">{{ $row['order_count'] ?? 0 }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-secondary">
                        <strong>Conversion Rate:</strong><br>
                        {{ $clientSatisfaction['conversion_rate'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-secondary">
                        <strong>Churn Rate:</strong><br>
                        {{ $clientSatisfaction['churn_rate'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Capacity Planning & Resource Utilization --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Capacity Planning & Resource Utilization</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-dark">
                        <strong>Peak Periods:</strong><br>
                        {{ $capacityPlanning['peak_periods'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-dark">
                        <strong>Workforce Availability:</strong><br>
                        {{ $capacityPlanning['workforce_availability'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-dark">
                        <strong>Machine Uptime:</strong><br>
                        {{ $capacityPlanning['machine_uptime'] ?? 'N/A' }}
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

code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.9em;
}
</style>
@endsection 