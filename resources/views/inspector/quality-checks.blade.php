@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Quality Checks</h2>
        <div>
            <a href="{{ route('inspector.quality-checks.create') }}" class="btn btn-primary">Create New Quality Check</a>
            <a href="{{ route('inspector.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>

    @if($qualityChecks->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Production Order</th>
                                <th>Check Type</th>
                                <th>Check Point</th>
                                <th>Status</th>
                                <th>Quality Score</th>
                                <th>Check Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($qualityChecks as $check)
                            <tr>
                                <td>{{ $check->productionOrder->product->name ?? 'N/A' }}</td>
                                <td>#{{ $check->productionOrder->id ?? 'N/A' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $check->check_type)) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $check->check_point)) }}</td>
                                <td>
                                    @if($check->pass_fail == 'pass')
                                        <span class="badge bg-success">Pass</span>
                                    @elseif($check->pass_fail == 'fail')
                                        <span class="badge bg-danger">Fail</span>
                                    @elseif($check->pass_fail == 'conditional_pass')
                                        <span class="badge bg-warning">Conditional Pass</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($check->quality_score)
                                        <span class="badge bg-{{ $check->quality_score >= 85 ? 'success' : ($check->quality_score >= 70 ? 'warning' : 'danger') }}">
                                            {{ $check->quality_score }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $check->check_date ? $check->check_date->format('M d, Y') : 'Not set' }}</td>
                                <td>
                                    <a href="{{ route('inspector.quality-checks.show', $check->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $qualityChecks->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-muted">No quality checks found</h5>
                <p class="text-muted">You haven't been assigned any quality checks yet.</p>
            </div>
        </div>
    @endif
</div>
@endsection 