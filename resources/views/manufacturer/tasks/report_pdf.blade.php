@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Workforce Auto Assignment Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Workforce Auto Assignment Report</h2>
    <p><strong>Date:</strong> {{ $date }}</p>
    <p><strong>Shift:</strong> {{ $shift }}</p>
    <table>
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
</body>
</html> 