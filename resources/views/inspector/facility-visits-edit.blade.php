@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Edit Facility Visit</h2>
    <form method="POST" action="{{ route('inspector.facility-visits.update', $facilityVisit->id) }}">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="vendor_id" class="form-label">Vendor</label>
                <select name="vendor_id" id="vendor_id" class="form-control" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @if($vendor->id == $facilityVisit->vendor_id) selected @endif>{{ $vendor->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="scheduled" @if($facilityVisit->status == 'scheduled') selected @endif>Scheduled</option>
                    <option value="in_progress" @if($facilityVisit->status == 'in_progress') selected @endif>In Progress</option>
                    <option value="completed" @if($facilityVisit->status == 'completed') selected @endif>Completed</option>
                    <option value="cancelled" @if($facilityVisit->status == 'cancelled') selected @endif>Cancelled</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="scheduled_date" class="form-label">Scheduled Date</label>
                <input type="datetime-local" name="scheduled_date" id="scheduled_date" class="form-control" value="{{ $facilityVisit->scheduled_date ? $facilityVisit->scheduled_date->format('Y-m-d\TH:i') : '' }}" required>
            </div>
            <div class="col-md-6">
                <label for="actual_visit_date" class="form-label">Actual Visit Date</label>
                <input type="datetime-local" name="actual_visit_date" id="actual_visit_date" class="form-control" value="{{ $facilityVisit->actual_visit_date ? $facilityVisit->actual_visit_date->format('Y-m-d\TH:i') : '' }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Inspection Results (key-value pairs)</label>
            <div id="inspection-results-list">
                @if(is_array($facilityVisit->inspection_results) && count($facilityVisit->inspection_results))
                    @foreach($facilityVisit->inspection_results as $i => $result)
                        <div class="row mb-2 inspection-result-row">
                            <div class="col-md-5"><input type="text" name="inspection_results[{{ $i }}][category]" class="form-control" value="{{ $i }}" placeholder="Category (e.g. cleanliness)"></div>
                            <div class="col-md-5"><input type="number" name="inspection_results[{{ $i }}][score]" class="form-control" value="{{ $result }}" placeholder="Score (0-100)" min="0" max="100"></div>
                            <div class="col-md-2"><button type="button" class="btn btn-danger remove-inspection-result">Remove</button></div>
                        </div>
                    @endforeach
                @else
                    <div class="row mb-2 inspection-result-row">
                        <div class="col-md-5"><input type="text" name="inspection_results[0][category]" class="form-control" placeholder="Category (e.g. cleanliness)"></div>
                        <div class="col-md-5"><input type="number" name="inspection_results[0][score]" class="form-control" placeholder="Score (0-100)" min="0" max="100"></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-inspection-result">Remove</button></div>
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-secondary" id="add-inspection-result">Add Inspection Result</button>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="visit_notes" class="form-label">Visit Notes</label>
                <textarea name="visit_notes" id="visit_notes" class="form-control" rows="4">{{ $facilityVisit->visit_notes }}</textarea>
            </div>
            <div class="col-md-6">
                <label for="passed_inspection" class="form-label">Passed Inspection</label>
                <select name="passed_inspection" id="passed_inspection" class="form-control">
                    <option value="">Select</option>
                    <option value="1" @if($facilityVisit->passed_inspection === true) selected @endif>Yes</option>
                    <option value="0" @if($facilityVisit->passed_inspection === false) selected @endif>No</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Facility Visit</button>
        <a href="{{ route('inspector.facility-visits') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script>
document.getElementById('add-inspection-result').addEventListener('click', function() {
    var list = document.getElementById('inspection-results-list');
    var index = list.getElementsByClassName('inspection-result-row').length;
    var row = document.createElement('div');
    row.className = 'row mb-2 inspection-result-row';
    row.innerHTML = `<div class=\"col-md-5\"><input type=\"text\" name=\"inspection_results[${index}][category]\" class=\"form-control\" placeholder=\"Category (e.g. cleanliness)\"></div><div class=\"col-md-5\"><input type=\"number\" name=\"inspection_results[${index}][score]\" class=\"form-control\" placeholder=\"Score (0-100)\" min=\"0\" max=\"100\"></div><div class=\"col-md-2\"><button type=\"button\" class=\"btn btn-danger remove-inspection-result\">Remove</button></div>`;
    list.appendChild(row);
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-inspection-result')) {
        e.target.closest('.inspection-result-row').remove();
    }
});
</script>
@endsection 