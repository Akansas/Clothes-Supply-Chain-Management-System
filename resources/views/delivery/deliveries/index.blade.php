@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">My Deliveries</h2>
            <p class="text-muted">A complete list of your assigned deliveries.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Recipient</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->order->order_number ?? 'N/A' }}</td>
                            <td>{{ $delivery->order->user->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($delivery->order->shipping_address ?? 'N/A', 40) }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($delivery->status) }}</span>
                            </td>
                            <td>{{ $delivery->estimated_delivery ? \Carbon\Carbon::parse($delivery->estimated_delivery)->format('M d, Y') : 'N/A' }}</td>
                            <td class="text-end">
                                <a href="{{ route('delivery.deliveries.show', $delivery->id) }}" class="btn btn-sm btn-outline-primary">View/Update</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">You have no assigned deliveries.</td>
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