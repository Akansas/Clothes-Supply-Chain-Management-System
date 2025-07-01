@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Welcome, Quality Inspector</h2>
    <p class="lead text-center mb-5">Monitor and manage quality control processes across the supply chain.</p>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $pendingChecks }}</h4>
                    <p class="card-text">Pending Checks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $completedThisMonth }}</h4>
                    <p class="card-text">Completed This Month</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $failedChecks }}</h4>
                    <p class="card-text">Failed Checks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $awaitingInspection }}</h4>
                    <p class="card-text">Awaiting Inspection</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <a href="{{ route('inspector.quality-checks') }}" class="btn btn-outline-primary w-100">Quality Checks</a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('inspector.facility-visits') }}" class="btn btn-outline-success w-100">Facility Visits</a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('inspector.reports') }}" class="btn btn-outline-warning w-100">Reports</a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="#" class="btn btn-outline-info w-100">Schedule Visit</a>
        </div>
    </div>

    <!-- Recent Quality Checks -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Quality Checks</h5>
                </div>
                <div class="card-body">
                    @if($qualityChecks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Production Order</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($qualityChecks as $check)
                                    <tr>
                                        <td>{{ $check->product->name ?? 'N/A' }}</td>
                                        <td>#{{ $check->productionOrder->id ?? 'N/A' }}</td>
                                        <td>
                                            @if($check->pass_fail == 'pass')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($check->pass_fail == 'fail')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $check->check_date ? $check->check_date->format('M d, Y') : $check->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No quality checks found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Facility Visits -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Facility Visits</h5>
                </div>
                <div class="card-body">
                    @if($facilityVisits->count() > 0)
                        @foreach($facilityVisits as $visit)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">{{ $visit->vendor->company_name ?? 'Unknown Facility' }}</h6>
                                <small class="text-muted">{{ $visit->getVisitDate()->format('M d, Y') }}</small>
                            </div>
                            <span class="badge bg-{{ $visit->status == 'completed' ? 'success' : ($visit->status == 'scheduled' ? 'warning' : 'info') }}">
                                {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                            </span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No facility visits found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications and Alerts -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light">Quality Alerts</div>
                <div class="card-body text-muted">
                    @if($failedChecks > 0)
                        <div class="alert alert-warning">
                            <strong>{{ $failedChecks }}</strong> quality checks have failed and require attention.
                        </div>
                    @else
                        <p class="text-success">All quality checks are passing standards.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light">Upcoming Tasks</div>
                <div class="card-body text-muted">
                    <ul class="list-unstyled">
                        <li>• Review pending quality checks</li>
                        <li>• Schedule facility inspections</li>
                        <li>• Update quality standards</li>
                        <li>• Generate monthly reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 