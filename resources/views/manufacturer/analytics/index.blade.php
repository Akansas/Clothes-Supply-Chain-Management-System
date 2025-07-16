@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-center mb-3">Manufacturer Analytics Dashboard</h2>
            <p class="text-muted text-center">Comprehensive insights for your manufacturing operations</p>
        </div>
    </div>

    {{-- Production Scheduling & Capacity Planning --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Production Scheduling & Capacity Planning</h5>
        </div>
        <div class="card-body">
            @if(!empty($productionScheduling['upcoming_orders']))
                <h6 class="text-primary">Upcoming Production Orders</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productionScheduling['upcoming_orders'] as $order)
                                <tr>
                                    <td>Order #{{ $order['id'] ?? $order->id }}</td>
                                    <td>
                                        <span class="badge {{ $order['status'] === 'completed' ? 'bg-success' : ($order['status'] === 'in_progress' ? 'bg-warning' : 'bg-info') }}">
                                            {{ $order['status'] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td>{{ $order['due_date'] ?? $order->due_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($productionScheduling['active_workforce']))
                <h6 class="text-primary mt-3">Active Workforce Allocations</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Operator ID</th>
                                <th>Date</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productionScheduling['active_workforce'] as $wf)
                                <tr>
                                    <td>{{ $wf['operator_id'] ?? $wf->operator_id }}</td>
                                    <td>{{ $wf['date'] ?? $wf->date }}</td>
                                    <td><span class="badge bg-primary">{{ $wf['shift'] ?? $wf->shift }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>Machine Availability:</strong><br>
                        {{ $productionScheduling['machine_availability'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>Demand Forecast:</strong><br>
                        {{ $productionScheduling['demand_forecast'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Material Consumption & Waste Tracking --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-recycle me-2"></i>Material Consumption & Waste Tracking</h5>
        </div>
        <div class="card-body">
            @if(!empty($materialConsumption['material_usage']))
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Used</th>
                                <th>Waste</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialConsumption['material_usage'] as $row)
                                <tr>
                                    <td>{{ $row['product']['name'] ?? $row->product->name ?? 'Unknown' }}</td>
                                    <td><span class="badge bg-info">{{ $row['quantity'] ?? $row->quantity }}</span></td>
                                    <td><span class="badge bg-success">{{ $row['used'] ?? $row->used }}</span></td>
                                    <td><span class="badge bg-warning">{{ $row['waste'] ?? $row->waste }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No material usage data available yet.
                </div>
            @endif

            <div class="alert alert-warning mt-3">
                <strong>Supplier Performance:</strong> {{ $materialConsumption['supplier_performance'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Order Fulfillment & Cycle Time Analysis --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Order Fulfillment & Cycle Time Analysis</h5>
        </div>
        <div class="card-body">
            @if(!empty($orderFulfillment['orders']))
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Status</th>
                                <th>Cycle Time (days)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderFulfillment['orders'] as $order)
                                <tr>
                                    <td>{{ $order['order_id'] }}</td>
                                    <td>{{ $order['product'] }}</td>
                                    <td>
                                        <span class="badge {{ $order['status'] === 'completed' ? 'bg-success' : ($order['status'] === 'in_progress' ? 'bg-warning' : 'bg-info') }}">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order['cycle_time_days'] !== null)
                                            <span class="badge bg-success">{{ $order['cycle_time_days'] }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No order fulfillment data available yet.
                </div>
            @endif

            <div class="alert alert-info mt-3">
                <strong>Phase Times:</strong> {{ $orderFulfillment['phase_times'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Labor Efficiency & Cost Analytics --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>Labor Efficiency & Cost Analytics</h5>
        </div>
        <div class="card-body">
            @if(!empty($laborEfficiency['output_per_operator']))
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Operator</th>
                                <th>Total Output</th>
                                <th>Total Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laborEfficiency['output_per_operator'] as $row)
                                <tr>
                                    <td>{{ $row['operator']['name'] ?? $row->operator->name ?? $row['operator_id'] ?? $row->operator_id }}</td>
                                    <td><span class="badge bg-warning">{{ $row['total_output'] ?? $row->total_output }}</span></td>
                                    <td><span class="badge bg-info">{{ $row['total_hours'] ?? $row->total_hours }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No labor efficiency data available yet.
                </div>
            @endif

            <div class="alert alert-warning mt-3">
                <strong>Overtime Patterns:</strong> {{ $laborEfficiency['overtime_patterns'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Quality Control & Defect Rate Analysis --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Quality Control & Defect Rate Analysis</h5>
        </div>
        <div class="card-body">
            @if(!empty($qualityControl['defect_rates']))
                <h6 class="text-danger">Defect Rates by Category</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Defect Category</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($qualityControl['defect_rates'] as $row)
                                <tr>
                                    <td>{{ $row['defect_category'] ?? 'Unknown' }}</td>
                                    <td><span class="badge bg-danger">{{ $row['count'] ?? 0 }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-danger">
                        <strong>Returns & Rework:</strong><br>
                        {{ $qualityControl['returns_and_rework'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>Benchmarking:</strong><br>
                        {{ $qualityControl['benchmarking'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cost Optimization & Profitability Analysis --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Cost Optimization & Profitability Analysis</h5>
        </div>
        <div class="card-body">
            @if(!empty($costOptimization['costs']))
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Labor Cost</th>
                                <th>Material Cost</th>
                                <th>Equipment Cost</th>
                                <th>Overhead Cost</th>
                                <th>Total Cost</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($costOptimization['costs'] as $row)
                                <tr>
                                    <td>{{ $row['product']['name'] ?? $row->product->name ?? 'Unknown' }}</td>
                                    <td><span class="badge bg-info">${{ number_format($row['labor_cost'] ?? $row->labor_cost, 2) }}</span></td>
                                    <td><span class="badge bg-success">${{ number_format($row['material_cost'] ?? $row->material_cost, 2) }}</span></td>
                                    <td><span class="badge bg-warning">${{ number_format($row['equipment_cost'] ?? $row->equipment_cost, 2) }}</span></td>
                                    <td><span class="badge bg-secondary">${{ number_format($row['overhead_cost'] ?? $row->overhead_cost, 2) }}</span></td>
                                    <td><span class="badge bg-primary">${{ number_format($row['total_cost'] ?? $row->total_cost, 2) }}</span></td>
                                    <td><span class="badge bg-dark">{{ $row['quantity'] ?? $row->quantity }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No cost optimization data available yet.
                </div>
            @endif

            <div class="alert alert-info mt-3">
                <strong>Margin Analysis:</strong> {{ $costOptimization['margin_analysis'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Workflow Automation & Alert Systems --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-robot me-2"></i>Workflow Automation & Alert Systems</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-dark">
                        <strong>Resource Usage:</strong><br>
                        {{ $workflowAlerts['resource_usage'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-dark">
                        <strong>Defect Rate:</strong><br>
                        {{ $workflowAlerts['defect_rate'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-dark">
                        <strong>Production Targets:</strong><br>
                        {{ $workflowAlerts['production_targets'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-dark">
                        <strong>Material Shortages:</strong><br>
                        {{ $workflowAlerts['material_shortages'] ?? 'N/A' }}
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