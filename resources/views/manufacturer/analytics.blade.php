@extends('layouts.app')  <!-- Or your dashboard layout -->

@section('content')
    <div class="container">
        <h1>Manufacturer Analytics Dashboard</h1>

        <div class="chart-section">
    <h2>Monthly Revenue</h2>
    <canvas id="monthlyRevenueChart"></canvas> <!-- Match this ID -->
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    fetch('/api/manufacturer/analytics/monthly-revenue')
        .then(response => response.json())
        .then(data => {
            console.log('Fetched chart data:', data);

            const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.months,
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: data.revenue,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
</script>
@endsection
