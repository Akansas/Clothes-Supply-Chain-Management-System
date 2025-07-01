@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Edit Quality Check</h2>
    <form method="POST" action="{{ route('inspector.quality-checks.update', $qualityCheck->id) }}">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="production_order_id" class="form-label">Production Order</label>
                <select name="production_order_id" id="production_order_id" class="form-control" required>
                    <option value="">Select Order</option>
                    @foreach($productionOrders as $order)
                        <option value="{{ $order->id }}" @if($order->id == $qualityCheck->production_order_id) selected @endif>#{{ $order->id }} - {{ $order->product->name ?? 'Product' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="vendor_id" class="form-label">Vendor</label>
                <select name="vendor_id" id="vendor_id" class="form-control" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @if($vendor->id == $qualityCheck->vendor_id) selected @endif>{{ $vendor->company_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="check_type" class="form-label">Check Type</label>
                <input type="text" name="check_type" id="check_type" class="form-control" value="{{ $qualityCheck->check_type }}" required>
            </div>
            <div class="col-md-4">
                <label for="check_point" class="form-label">Check Point</label>
                <input type="text" name="check_point" id="check_point" class="form-control" value="{{ $qualityCheck->check_point }}" required>
            </div>
            <div class="col-md-4">
                <label for="check_date" class="form-label">Check Date</label>
                <input type="datetime-local" name="check_date" id="check_date" class="form-control" value="{{ $qualityCheck->check_date ? $qualityCheck->check_date->format('Y-m-d\TH:i') : '' }}" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="sample_size" class="form-label">Sample Size</label>
                <input type="number" name="sample_size" id="sample_size" class="form-control" min="1" value="{{ $qualityCheck->sample_size }}" required>
            </div>
            <div class="col-md-3">
                <label for="defects_found" class="form-label">Defects Found</label>
                <input type="number" name="defects_found" id="defects_found" class="form-control" min="0" value="{{ $qualityCheck->defects_found }}" required>
            </div>
            <div class="col-md-3">
                <label for="quality_score" class="form-label">Quality Score (%)</label>
                <input type="number" name="quality_score" id="quality_score" class="form-control" min="0" max="100" value="{{ $qualityCheck->quality_score }}" required>
            </div>
            <div class="col-md-3">
                <label for="pass_fail" class="form-label">Result</label>
                <select name="pass_fail" id="pass_fail" class="form-control" required>
                    <option value="">Select</option>
                    <option value="pass" @if($qualityCheck->pass_fail == 'pass') selected @endif>Pass</option>
                    <option value="fail" @if($qualityCheck->pass_fail == 'fail') selected @endif>Fail</option>
                    <option value="conditional_pass" @if($qualityCheck->pass_fail == 'conditional_pass') selected @endif>Conditional Pass</option>
                    <option value="pending" @if($qualityCheck->pass_fail == 'pending') selected @endif>Pending</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Defect Types (key-value pairs)</label>
            <div id="defect-types-list">
                @if(is_array($qualityCheck->defect_types) && count($qualityCheck->defect_types))
                    @foreach($qualityCheck->defect_types as $i => $pair)
                        <div class="row mb-2 defect-type-row">
                            <div class="col-md-5"><input type="text" name="defect_types[{{ $i }}][type]" class="form-control" value="{{ $i }}" placeholder="Type (e.g. stitching_error)"></div>
                            <div class="col-md-5"><input type="number" name="defect_types[{{ $i }}][count]" class="form-control" value="{{ $pair }}" placeholder="Count" min="0"></div>
                            <div class="col-md-2"><button type="button" class="btn btn-danger remove-defect-type">Remove</button></div>
                        </div>
                    @endforeach
                @else
                    <div class="row mb-2 defect-type-row">
                        <div class="col-md-5"><input type="text" name="defect_types[0][type]" class="form-control" placeholder="Type (e.g. stitching_error)"></div>
                        <div class="col-md-5"><input type="number" name="defect_types[0][count]" class="form-control" placeholder="Count" min="0"></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-defect-type">Remove</button></div>
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-secondary" id="add-defect-type">Add Defect Type</button>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control">{{ $qualityCheck->notes }}</textarea>
            </div>
            <div class="col-md-6">
                <label for="corrective_actions" class="form-label">Corrective Actions</label>
                <textarea name="corrective_actions" id="corrective_actions" class="form-control">{{ $qualityCheck->corrective_actions }}</textarea>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="recheck_required" class="form-label">Recheck Required</label>
                <select name="recheck_required" id="recheck_required" class="form-control">
                    <option value="0" @if(!$qualityCheck->recheck_required) selected @endif>No</option>
                    <option value="1" @if($qualityCheck->recheck_required) selected @endif>Yes</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="recheck_date" class="form-label">Recheck Date</label>
                <input type="datetime-local" name="recheck_date" id="recheck_date" class="form-control" value="{{ $qualityCheck->recheck_date ? \Carbon\Carbon::parse($qualityCheck->recheck_date)->format('Y-m-d\TH:i') : '' }}">
            </div>
            <div class="col-md-4">
                <label for="is_critical" class="form-label">Is Critical?</label>
                <select name="is_critical" id="is_critical" class="form-control">
                    <option value="0" @if(!$qualityCheck->is_critical) selected @endif>No</option>
                    <option value="1" @if($qualityCheck->is_critical) selected @endif>Yes</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Quality Check</button>
        <a href="{{ route('inspector.quality-checks') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script>
document.getElementById('add-defect-type').addEventListener('click', function() {
    var list = document.getElementById('defect-types-list');
    var index = list.getElementsByClassName('defect-type-row').length;
    var row = document.createElement('div');
    row.className = 'row mb-2 defect-type-row';
    row.innerHTML = `<div class=\"col-md-5\"><input type=\"text\" name=\"defect_types[${index}][type]\" class=\"form-control\" placeholder=\"Type (e.g. stitching_error)\"></div><div class=\"col-md-5\"><input type=\"number\" name=\"defect_types[${index}][count]\" class=\"form-control\" placeholder=\"Count\" min=\"0\"></div><div class=\"col-md-2\"><button type=\"button\" class=\"btn btn-danger remove-defect-type\">Remove</button></div>`;
    list.appendChild(row);
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-defect-type')) {
        e.target.closest('.defect-type-row').remove();
    }
});
</script>
@endsection 