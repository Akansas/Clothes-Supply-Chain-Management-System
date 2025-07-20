@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Workforce Distribution Report</h2>
    <form method="GET" class="mb-3" id="filterForm">
        <div class="row">
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-3">
                <label for="shift" class="form-label">Shift</label>
                <select name="shift" id="shift" class="form-control">
                    <option value="" @if(empty($shift)) selected @endif>All</option>
                    <option value="Morning" @if($shift == 'Morning') selected @endif>Morning</option>
                    <option value="Evening" @if($shift == 'Evening') selected @endif>Evening</option>
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="#" class="btn btn-success ms-2" id="downloadPdfBtn">Download PDF</a>
            </div>
        </div>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('downloadPdfBtn');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var date = document.getElementById('date').value;
                var shift = document.getElementById('shift').value;
                var url = "{{ route('workforce.report.pdf') }}?date=" + encodeURIComponent(date) + "&shift=" + encodeURIComponent(shift);
                window.location.href = url;
            });
        }
    });
    </script>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Worker</th>
                <th>Skill</th>
                <th>Task</th>
                <th>Shift</th>
                <th>Center</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result['worker'] }}</td>
                    <td>{{ $result['skill'] }}</td>
                    <td>{{ $result['task'] }}</td>
                    <td>{{ $result['shift'] }}</td>
                    <td>{{ $result['center'] }}</td>
                    <td>{{ $result['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 