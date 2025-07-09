@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Vendor Validation Dashboard</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @php
        $latestApplication = $vendor->applications()->latest()->first();
        $latestVisit = $vendor->facilityVisits()->latest()->first();
    @endphp

    @if(!$latestApplication || ($latestApplication && $latestApplication->status === 'rejected'))
        <!-- No application or rejected: show upload form -->
        <div class="card mb-4">
            <div class="card-header">{{ $latestApplication && $latestApplication->status === 'rejected' ? 'Reapply as Vendor' : 'Submit Vendor Application' }}</div>
            <div class="card-body">
                @if($latestApplication && $latestApplication->status === 'rejected')
                    <div class="alert alert-warning mb-3">
                        Your previous application was rejected. Please review the notes and reapply.
                    </div>
                @endif
                <form action="{{ route('vendor.applications.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="pdf" class="form-label">Upload Application PDF</label>
                        <input type="file" class="form-control" id="pdf" name="pdf" accept="application/pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ $latestApplication && $latestApplication->status === 'rejected' ? 'Reapply' : 'Submit Application' }}</button>
                </form>
            </div>
        </div>
    @else
        <!-- Application exists: show status and results -->
        <div class="card mb-4">
            <div class="card-header">Application Status</div>
            <div class="card-body">
                <p><strong>Status:</strong> <span class="badge bg-{{ $latestApplication->status === 'approved' ? 'success' : ($latestApplication->status === 'pending' ? 'warning' : ($latestApplication->status === 'validating' ? 'info' : 'danger')) }}">{{ ucfirst($latestApplication->status) }}</span></p>
                <p><strong>Application PDF:</strong> <a href="{{ asset('storage/' . $latestApplication->pdf_path) }}" target="_blank">Download/View</a></p>
                @if($latestApplication->status === 'validating')
                    <div class="alert alert-info">Your application is being validated. Please wait for results.</div>
                @endif
                @if($latestApplication->validation_results)
                    @php $results = json_decode($latestApplication->validation_results, true); @endphp
                    <h5 class="mt-4">Validation Results</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Financial Stability
                            <span class="badge bg-{{ ($results['financial_stability'] ?? 0) >= 0.7 ? 'success' : 'danger' }}">{{ $results['financial_stability'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Reputation
                            <span class="badge bg-{{ ($results['reputation'] ?? 0) >= 0.7 ? 'success' : 'danger' }}">{{ $results['reputation'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Regulatory Compliance
                            <span class="badge bg-{{ ($results['compliance'] ?? 0) >= 0.7 ? 'success' : 'danger' }}">{{ $results['compliance'] ?? 'N/A' }}</span>
                        </li>
                    </ul>
                @endif
                @if($latestApplication->validation_notes)
                    <div class="alert alert-info">
                        <strong>Validation Notes:</strong> {{ $latestApplication->validation_notes }}
                    </div>
                @endif
                @if($latestApplication->status === 'approved' && $latestVisit)
                    <h5 class="mt-4">Facility Visit</h5>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $latestVisit->status === 'completed' ? 'success' : ($latestVisit->status === 'scheduled' ? 'info' : 'warning') }}">{{ ucfirst($latestVisit->status) }}</span></p>
                    <p><strong>Scheduled Date:</strong> {{ $latestVisit->scheduled_date ? $latestVisit->scheduled_date->format('M d, Y') : 'N/A' }}</p>
                    @if($latestVisit->visit_notes)
                        <div class="alert alert-secondary">
                            <strong>Visit Notes:</strong> {{ $latestVisit->visit_notes }}
                        </div>
                    @endif
                @elseif($latestApplication->status === 'approved')
                    <div class="alert alert-warning">Facility visit will be scheduled soon. Please check back later.</div>
                @endif
                <div class="mt-4">
                    <h6>Next Steps:</h6>
                    @if($latestApplication->status === 'pending')
                        <p>Your application is pending review. Please wait for validation.</p>
                    @elseif($latestApplication->status === 'validating')
                        <p>Your application is being validated. Please wait for results.</p>
                    @elseif($latestApplication->status === 'approved')
                        <p>Your application has been approved. Prepare for the facility visit.</p>
                    @elseif($latestApplication->status === 'rejected')
                        <p>Your application was rejected. Please review the notes and consider reapplying.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 