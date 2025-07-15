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

    <!-- Analytics Section -->
    @if(isset($systemKpis) || isset($userActivity) || isset($workflowPerformance) || isset($compliance) || isset($riskDashboard) || isset($alertsSummary))
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Admin Analytics</h4>
        </div>
        <div class="card-body">
            @if(isset($systemKpis))
                <h5>System KPIs</h5>
                <pre>{{ json_encode($systemKpis, JSON_PRETTY_PRINT) }}</pre>
            @endif
            @if(isset($userActivity))
                <h5>User Activity</h5>
                <pre>{{ json_encode($userActivity, JSON_PRETTY_PRINT) }}</pre>
            @endif
            @if(isset($workflowPerformance))
                <h5>Workflow Performance</h5>
                <pre>{{ json_encode($workflowPerformance, JSON_PRETTY_PRINT) }}</pre>
            @endif
            @if(isset($compliance))
                <h5>Compliance & Audit</h5>
                <pre>{{ json_encode($compliance, JSON_PRETTY_PRINT) }}</pre>
            @endif
            @if(isset($riskDashboard))
                <h5>Risk & Resilience</h5>
                <pre>{{ json_encode($riskDashboard, JSON_PRETTY_PRINT) }}</pre>
            @endif
            @if(isset($alertsSummary))
                <h5>Alerts & Executive Summaries</h5>
                <pre>{{ json_encode($alertsSummary, JSON_PRETTY_PRINT) }}</pre>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection 