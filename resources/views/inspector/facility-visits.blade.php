@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Facility Visits</h2>
        <div>
            <a href="{{ route('inspector.facility-visits.create') }}" class="btn btn-primary">Create New Facility Visit</a>
            <a href="{{ route('inspector.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>

    @if($facilityVisits->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Facility Name</th>
                                <th>Scheduled Date</th>
                                <th>Actual Visit Date</th>
                                <th>Status</th>
                                <th>Inspection Result</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facilityVisits as $visit)
                            <tr>
                                <td>{{ $visit->vendor->company_name ?? 'Unknown Facility' }}</td>
                                <td>{{ $visit->scheduled_date->format('M d, Y') }}</td>
                                <td>{{ $visit->actual_visit_date ? $visit->actual_visit_date->format('M d, Y') : 'Not visited' }}</td>
                                <td>
                                    @if($visit->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($visit->status == 'scheduled')
                                        <span class="badge bg-warning">Scheduled</span>
                                    @elseif($visit->status == 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($visit->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($visit->passed_inspection === true)
                                        <span class="text-success">Passed</span>
                                    @elseif($visit->passed_inspection === false)
                                        <span class="text-danger">Failed</span>
                                    @else
                                        <span class="text-muted">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('inspector.facility-visits.show', $visit->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $facilityVisits->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-muted">No facility visits found</h5>
                <p class="text-muted">You haven't been assigned any facility visits yet.</p>
            </div>
        </div>
    @endif
</div>
@endsection 