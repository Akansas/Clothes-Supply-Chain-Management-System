@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Deliveries</h2>
            <p class="text-muted">Track and update the status of your outgoing deliveries.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Delivery ID</th>
                            <th>Order ID</th>
                            <th>Manufacturer</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                        <tr>
                            <td>#{{ $delivery->id }}</td>
                            <td>#{{ $delivery->order->id ?? 'N/A' }}</td>
                            <td>{{ $delivery->order->manufacturer->name ?? 'N/A' }}</td>
                            <td>{{ $delivery->driver->user->name ?? 'Unassigned' }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($delivery->status) }}</span></td>
                            <td>{{ $delivery->updated_at->format('M d, Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('supplier.deliveries.show', $delivery) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No deliveries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $deliveries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 