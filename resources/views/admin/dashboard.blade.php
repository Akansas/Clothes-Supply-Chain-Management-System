@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>

    {{-- System KPIs --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">System KPIs</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tr><th>Fulfillment Rate</th><td>{{ isset($systemKpis->fulfillment_rate) ? number_format($systemKpis->fulfillment_rate * 100, 2) . '%' : 'N/A' }}</td></tr>
                <tr><th>Avg Lead Time (days)</th><td>{{ $systemKpis->avg_lead_time_days ?? 'N/A' }}</td></tr>
                <tr><th>Cost Efficiency</th><td>{{ isset($systemKpis->cost_efficiency) ? number_format($systemKpis->cost_efficiency, 2) : 'N/A' }}</td></tr>
                <tr><th>Service Level</th><td>{{ isset($systemKpis->service_level) ? number_format($systemKpis->service_level * 100, 2) . '%' : 'N/A' }}</td></tr>
            </table>
            <h5>Department Comparison</h5>
            <table class="table table-bordered">
                <thead><tr><th>Department</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($systemKpis->department_comparison ?? [] as $dept => $count)
                        <tr><td>{{ ucfirst($dept) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <h5>Trends (Last 6 Months)</h5>
            <table class="table">
                <thead><tr><th>Month</th><th>Orders</th></tr></thead>
                <tbody>
                    @foreach($systemKpis->trends ?? [] as $month => $count)
                        <tr><td>{{ $month }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- User Activity --}}
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">User Activity & Role Analysis</h4>
        </div>
        <div class="card-body">
            <h5>Login Patterns (Last 6 Weeks)</h5>
            <table class="table table-bordered">
                <thead><tr><th>Week Start</th><th>Logins</th></tr></thead>
                <tbody>
                    @foreach($userActivity->login_patterns ?? [] as $week => $count)
                        <tr><td>{{ $week }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <h5>Permission Usage</h5>
            <table class="table table-bordered">
                <thead><tr><th>Role</th><th>User Count</th></tr></thead>
                <tbody>
                    @foreach($userActivity->permission_usage ?? [] as $role => $count)
                        <tr><td>{{ ucfirst($role) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <h5>Top Audit Trails</h5>
            <table class="table table-bordered">
                <thead><tr><th>Name</th><th>Email</th><th>Audit Logs</th></tr></thead>
                <tbody>
                    @foreach($userActivity->audit_trails ?? [] as $user)
                        <tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->report_logs_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <h5>Anomalies</h5>
            @if(isset($userActivity->anomalies) && count($userActivity->anomalies))
                <ul>
                    @foreach($userActivity->anomalies as $user)
                        <li>{{ $user->name }} ({{ $user->email }}) - {{ $user->report_logs_count }} logs</li>
                    @endforeach
                </ul>
            @else
                <p>No anomalies detected.</p>
            @endif
        </div>
    </div>

    {{-- Workflow Performance --}}
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Workflow & Process Performance</h4>
        </div>
        <div class="card-body">
            <h5>Order Throughput (Last 6 Weeks)</h5>
            <table class="table table-bordered">
                <thead><tr><th>Week Start</th><th>Orders</th></tr></thead>
                <tbody>
                    @foreach($workflowPerformance->order_throughput ?? [] as $week => $count)
                        <tr><td>{{ $week }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <ul>
                <li>Delivery Cycles (avg days): {{ $workflowPerformance->delivery_cycles ?? 'N/A' }}</li>
                <li>Bottlenecks (pending > 7 days): {{ $workflowPerformance->bottlenecks ?? 'N/A' }}</li>
                <li>Exception Handling: {{ $workflowPerformance->exception_handling ?? 'N/A' }}</li>
            </ul>
        </div>
    </div>

    {{-- Compliance & Audit Reporting --}}
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Compliance & Audit Reporting</h4>
        </div>
        <div class="card-body">
            <ul>
                <li>Inspection Logs: {{ $compliance->inspection_logs ?? 'N/A' }}</li>
                <li>Quality Audits - Pass Rate: {{ isset($compliance->quality_audits->pass_rate) ? number_format($compliance->quality_audits->pass_rate * 100, 2) . '%' : 'N/A' }}, Fail Rate: {{ isset($compliance->quality_audits->fail_rate) ? number_format($compliance->quality_audits->fail_rate * 100, 2) . '%' : 'N/A' }}</li>
                <li>Compliance Flags: {{ $compliance->compliance_flags ?? 'N/A' }}</li>
                <li>Regulation Adherence: {{ isset($compliance->regulation_adherence) ? number_format($compliance->regulation_adherence * 100, 2) . '%' : 'N/A' }}</li>
                <li>Corrective Actions: {{ $compliance->corrective_actions ?? 'N/A' }}</li>
            </ul>
        </div>
    </div>

    {{-- Risk & Resilience Dashboards --}}
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Risk & Resilience Dashboards</h4>
        </div>
        <div class="card-body">
            <ul>
                <li>Overdue Orders: {{ $riskDashboard->risk_indicators->overdue_orders ?? 'N/A' }}</li>
                <li>Supplier Reliability (vendors with high reputation): {{ $riskDashboard->supplier_reliability ?? 'N/A' }}</li>
            </ul>
        </div>
    </div>

    {{-- Alerts & Executive Summaries --}}
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0">Alerts & Executive Summaries</h4>
        </div>
        <div class="card-body">
            <ul>
                <li>Cost Spikes: {{ $alertsSummary->real_time_alerts->cost_spikes ?? 'N/A' }}</li>
                <li>Stockouts: {{ $alertsSummary->real_time_alerts->stockouts ?? 'N/A' }}</li>
                <li>Total Orders: {{ $alertsSummary->executive_summaries->total_orders ?? 'N/A' }}</li>
                <li>Total Revenue: ${{ isset($alertsSummary->executive_summaries->total_revenue) ? number_format($alertsSummary->executive_summaries->total_revenue, 2) : 'N/A' }}</li>
                <li>Total Users: {{ $alertsSummary->executive_summaries->total_users ?? 'N/A' }}</li>
            </ul>
        </div>
    </div>

    <h3>Customer Segments</h3>
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Segment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($segments as $seg)
                <tr>
                    <td>{{ $seg['id'] }}</td>
                    <td>{{ $seg['segment'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Demand Forecast (Next 14 Days)</h3>
    <canvas id="forecastChart"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const forecast = @json($forecast);
        const labels = Object.keys(forecast);
        const data = Object.values(forecast);
        new Chart(document.getElementById('forecastChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Predicted Sales',
                    data: data,
                    borderColor: 'blue',
                    fill: false
                }]
            }
        });
    </script>

    <!-- Analytics Section -->
    @if(isset($systemKpis) || isset($userActivity) || isset($workflowPerformance) || isset($compliance) || isset($riskDashboard) || isset($alertsSummary))
    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header bg-info text-white d-flex align-items-center">
            <i class="fas fa-chart-bar fa-lg me-2"></i>
            <h4 class="mb-0">Admin Analytics</h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @if(isset($systemKpis))
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-tachometer-alt fa-2x text-primary me-2"></i>
                                <h6 class="mb-0">System KPIs</h6>
                            </div>
                            <div class="text-muted small mb-2">Key performance indicators for the system.</div>
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Fulfillment Rate
                                        <span class="fw-bold">{{ isset($systemKpis->fulfillment_rate) ? number_format($systemKpis->fulfillment_rate * 100, 2) . '%' : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Avg Lead Time (days)
                                        <span class="fw-bold">{{ $systemKpis->avg_lead_time_days ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Cost Efficiency
                                        <span class="fw-bold">{{ isset($systemKpis->cost_efficiency) ? number_format($systemKpis->cost_efficiency, 2) : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Service Level
                                        <span class="fw-bold">{{ isset($systemKpis->service_level) ? number_format($systemKpis->service_level * 100, 2) . '%' : 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(isset($userActivity))
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-friends fa-2x text-success me-2"></i>
                                <h6 class="mb-0">User Activity</h6>
                            </div>
                            <div class="text-muted small mb-2">Recent user activity and role analysis.</div>
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Permission Usage
                                        <span class="fw-bold">{{ is_array($userActivity->permission_usage) ? count($userActivity->permission_usage) : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Audit Trails
                                        <span class="fw-bold">{{ is_array($userActivity->audit_trails) ? count($userActivity->audit_trails) : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Anomalies
                                        <span class="fw-bold">{{ is_array($userActivity->anomalies) ? count($userActivity->anomalies) : 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(isset($workflowPerformance))
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-cogs fa-2x text-warning me-2"></i>
                                <h6 class="mb-0">Workflow Performance</h6>
                            </div>
                            <div class="text-muted small mb-2">Order throughput and bottlenecks.</div>
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Order Throughput
                                        <span class="fw-bold">{{ is_array($workflowPerformance->order_throughput) ? count($workflowPerformance->order_throughput) : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Delivery Cycles
                                        <span class="fw-bold">{{ $workflowPerformance->delivery_cycles ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Bottlenecks
                                        <span class="fw-bold">{{ $workflowPerformance->bottlenecks ?? 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(isset($compliance))
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clipboard-check fa-2x text-secondary me-2"></i>
                                <h6 class="mb-0">Compliance & Audit</h6>
                            </div>
                            <div class="text-muted small mb-2">Compliance, audits, and corrective actions.</div>
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Inspection Logs
                                        <span class="fw-bold">{{ $compliance->inspection_logs ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Quality Audits (Pass Rate)
                                        <span class="fw-bold">{{ isset($compliance->quality_audits->pass_rate) ? number_format($compliance->quality_audits->pass_rate * 100, 2) . '%' : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Compliance Flags
                                        <span class="fw-bold">{{ $compliance->compliance_flags ?? 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(isset($riskDashboard))
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger me-2"></i>
                                <h6 class="mb-0">Risk & Resilience</h6>
                            </div>
                            <div class="text-muted small mb-2">Risk indicators and supplier reliability.</div>
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Overdue Orders
                                        <span class="fw-bold">{{ $riskDashboard->risk_indicators->overdue_orders ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Supplier Reliability
                                        <span class="fw-bold">{{ $riskDashboard->supplier_reliability ?? 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(isset($alertsSummary))
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-bell fa-2x text-info me-2"></i>
                                <h6 class="mb-0">Alerts & Executive Summaries</h6>
                            </div>
                            <div class="text-muted small mb-2">Real-time alerts and executive summaries.</div>
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Cost Spikes
                                        <span class="fw-bold">{{ $alertsSummary->real_time_alerts->cost_spikes ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Stockouts
                                        <span class="fw-bold">{{ $alertsSummary->real_time_alerts->stockouts ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Orders
                                        <span class="fw-bold">{{ $alertsSummary->executive_summaries->total_orders ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Revenue
                                        <span class="fw-bold">${{ isset($alertsSummary->executive_summaries->total_revenue) ? number_format($alertsSummary->executive_summaries->total_revenue, 2) : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Users
                                        <span class="fw-bold">{{ $alertsSummary->executive_summaries->total_users ?? 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 