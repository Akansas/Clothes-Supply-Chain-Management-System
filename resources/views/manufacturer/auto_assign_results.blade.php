@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Auto Assignment Results</h2>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary mb-3">Back to Tasks</a>
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