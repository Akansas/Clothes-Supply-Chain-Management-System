@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Facility Visit Details</h2>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>ID</th><td>{{ $facilityVisit->id }}</td></tr>
                <tr><th>Vendor</th><td>{{ $facilityVisit->vendor->company_name ?? 'Unknown' }}</td></tr>
                <tr><th>Inspector</th><td>{{ $facilityVisit->inspector->name ?? '' }}</td></tr>
                <tr><th>Scheduled Date</th><td>{{ $facilityVisit->scheduled_date->format('M d, Y H:i') }}</td></tr>
                <tr><th>Actual Visit Date</th><td>{{ $facilityVisit->actual_visit_date ? $facilityVisit->actual_visit_date->format('M d, Y H:i') : 'Not visited' }}</td></tr>
                <tr><th>Status</th><td>{{ ucfirst(str_replace('_', ' ', $facilityVisit->status)) }}</td></tr>
                <tr><th>Passed Inspection</th><td>{{ $facilityVisit->passed_inspection === true ? 'Yes' : ($facilityVisit->passed_inspection === false ? 'No' : 'Not determined') }}</td></tr>
                <tr><th>Visit Notes</th><td>{{ $facilityVisit->visit_notes ?? 'No notes' }}</td></tr>
                <tr><th>Inspection Results</th><td>
                    @php $results = is_array($facilityVisit->inspection_results) ? $facilityVisit->inspection_results : json_decode($facilityVisit->inspection_results, true); @endphp
                    @if($results && count($results))
                        <ul>
                        @foreach($results as $category => $score)
                            <li>{{ ucfirst(str_replace('_', ' ', $category)) }}: {{ $score }}/100</li>
                        @endforeach
                        </ul>
                    @else
                        <span class="text-muted">No results recorded</span>
                    @endif
                </td></tr>
            </table>
            <a href="{{ route('inspector.facility-visits.edit', $facilityVisit->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('inspector.facility-visits') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection 