@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Inventory Adjustments Log</h2>
    <a href="{{ route('inventory.adjustments.create') }}" class="btn btn-primary mb-3">New Adjustment</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>User</th>
                <th>Reason</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adjustments as $adj)
                <tr>
                    <td>{{ $adj->id }}</td>
                    <td>
                        @if($adj->rawMaterial)
                            Raw Material: {{ $adj->rawMaterial->name }}
                        @elseif($adj->finishedGood)
                            Finished Good: {{ $adj->finishedGood->product_name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ ucfirst($adj->adjustment_type) }}</td>
                    <td>{{ $adj->quantity }}</td>
                    <td>{{ $adj->user->name ?? 'N/A' }}</td>
                    <td>{{ $adj->reason }}</td>
                    <td>{{ $adj->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $adjustments->links() }}
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary mt-3">Back to Inventory</a>
</div>
@endsection 