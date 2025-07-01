@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="fw-bold mb-0">Delivery Details</h2>
                    <p class="text-muted">For Order #{{ $delivery->order->order_number ?? 'N/A' }}</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h5 class="fw-bold">Recipient Information</h5>
                    <p><strong>Name:</strong> {{ $delivery->order->user->name ?? 'N/A' }}<br>
                       <strong>Address:</strong> {{ $delivery->order->shipping_address ?? 'N/A' }}</p>

                    <h5 class="fw-bold mt-4">Current Status: <span class="badge bg-primary">{{ ucfirst($delivery->status) }}</span></h5>

                    <form action="{{ route('delivery.deliveries.update-status', $delivery->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">Update Delivery Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" @if($delivery->status == 'pending') selected @endif>Pending</option>
                                <option value="in_transit" @if($delivery->status == 'in_transit') selected @endif>In Transit</option>
                                <option value="delivered" @if($delivery->status == 'delivered') selected @endif>Delivered</option>
                                <option value="failed" @if($delivery->status == 'failed') selected @endif>Failed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('delivery.deliveries') }}" class="btn btn-secondary me-2">Back to List</a>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 