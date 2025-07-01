@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Quality Reports</h2>
        <a href="{{ route('inspector.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <!-- Monthly Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Quality Check Statistics ({{ now()->year }})</h5>
                </div>
                <div class="card-body">
                    @if($monthlyStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Checks</th>
                                        <th>Passed</th>
                                        <th>Failed</th>
                                        <th>Pass Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyStats as $stat)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::create()->month($stat->month)->format('F') }}</td>
                                        <td>{{ $stat->total }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $stat->completed }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $stat->failed }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $passRate = $stat->total > 0 ? ($stat->completed / $stat->total) * 100 : 0;
                                            @endphp
                                            <span class="badge bg-{{ $passRate >= 90 ? 'success' : ($passRate >= 80 ? 'warning' : 'danger') }}">
                                                {{ number_format($passRate, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No quality check data available for this year.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $monthlyStats->sum('total') }}</h4>
                    <p class="card-text">Total Checks This Year</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $monthlyStats->sum('completed') }}</h4>
                    <p class="card-text">Total Passed</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $monthlyStats->sum('failed') }}</h4>
                    <p class="card-text">Total Failed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Reports -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Quality Trends</h5>
                </div>
                <div class="card-body text-muted">
                    <p>Track quality performance over time and identify improvement areas.</p>
                    <a href="#" class="btn btn-outline-primary">Generate Trend Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Defect Analysis</h5>
                </div>
                <div class="card-body text-muted">
                    <p>Analyze common defects and their root causes.</p>
                    <a href="#" class="btn btn-outline-primary">View Defect Report</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 