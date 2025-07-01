@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Quality Check Details</h2>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>ID</th><td>{{ $qualityCheck->id }}</td></tr>
                <tr><th>Production Order</th><td>#{{ $qualityCheck->production_order_id }} - {{ $qualityCheck->productionOrder->product->name ?? 'Product' }}</td></tr>
                <tr><th>Vendor</th><td>{{ $qualityCheck->vendor_id }}</td></tr>
                <tr><th>Check Type</th><td>{{ $qualityCheck->check_type }}</td></tr>
                <tr><th>Check Point</th><td>{{ $qualityCheck->check_point }}</td></tr>
                <tr><th>Check Date</th><td>{{ $qualityCheck->check_date ? $qualityCheck->check_date->format('M d, Y H:i') : '' }}</td></tr>
                <tr><th>Sample Size</th><td>{{ $qualityCheck->sample_size }}</td></tr>
                <tr><th>Defects Found</th><td>{{ $qualityCheck->defects_found }}</td></tr>
                <tr><th>Defect Types</th><td>
                    @php $defects = is_array($qualityCheck->defect_types) ? $qualityCheck->defect_types : json_decode($qualityCheck->defect_types, true); @endphp
                    @if($defects && count($defects))
                        <ul>
                        @foreach($defects as $type => $count)
                            <li>{{ $type }}: {{ $count }}</li>
                        @endforeach
                        </ul>
                    @else
                        <span class="text-muted">None</span>
                    @endif
                </td></tr>
                <tr><th>Quality Score</th><td>{{ $qualityCheck->quality_score }}%</td></tr>
                <tr><th>Result</th><td>{{ ucfirst(str_replace('_', ' ', $qualityCheck->pass_fail)) }}</td></tr>
                <tr><th>Notes</th><td>{{ $qualityCheck->notes }}</td></tr>
                <tr><th>Corrective Actions</th><td>{{ $qualityCheck->corrective_actions }}</td></tr>
                <tr><th>Recheck Required</th><td>{{ $qualityCheck->recheck_required ? 'Yes' : 'No' }}</td></tr>
                <tr><th>Recheck Date</th><td>{{ $qualityCheck->recheck_date ? \Carbon\Carbon::parse($qualityCheck->recheck_date)->format('M d, Y H:i') : '-' }}</td></tr>
                <tr><th>Is Critical</th><td>{{ $qualityCheck->is_critical ? 'Yes' : 'No' }}</td></tr>
                <tr><th>Inspector</th><td>{{ $qualityCheck->inspector->name ?? '' }}</td></tr>
            </table>
            <a href="{{ route('inspector.quality-checks.edit', $qualityCheck->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('inspector.quality-checks') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection 