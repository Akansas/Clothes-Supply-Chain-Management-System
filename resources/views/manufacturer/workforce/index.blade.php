@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Workforce Management</h2>
    <h4>Workers</h4>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Role</th><th>Skills</th></tr></thead>
        <tbody>
        @foreach($workers as $worker)
            <tr>
                <td>{{ $worker->name }}</td>
                <td>{{ $worker->role }}</td>
                <td>{{ $worker->skills }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <h4>Shifts</h4>
    <table class="table table-bordered">
        <thead><tr><th>Center</th><th>Date</th><th>Start</th><th>End</th><th>Required Workers</th></tr></thead>
        <tbody>
        @foreach($shifts as $shift)
            <tr>
                <td>{{ $shift->center }}</td>
                <td>{{ $shift->date }}</td>
                <td>{{ $shift->start_time }}</td>
                <td>{{ $shift->end_time }}</td>
                <td>{{ $shift->required_workers }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <h4>Assignments</h4>
    <table class="table table-bordered">
        <thead><tr><th>Worker</th><th>Shift</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($assignments as $assignment)
            <tr>
                <td>{{ $assignment->worker->name ?? 'N/A' }}</td>
                <td>{{ $assignment->shift->center ?? 'N/A' }} ({{ $assignment->shift->date ?? '' }})</td>
                <td>{{ $assignment->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection 