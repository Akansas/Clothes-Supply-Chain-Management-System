@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="fw-bold mb-0">Delivery #{{ $delivery->id }}</h2>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <dl class="row mb-4">
                        <dt class="col-sm-4">Order ID</dt>
                        <dd class="col-sm-8">#{{ $delivery->order->id ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Manufacturer</dt>
                        <dd class="col-sm-8">{{ $delivery->order->manufacturer->name ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Driver</dt>
                        <dd class="col-sm-8">{{ $delivery->driver->user->name ?? 'Unassigned' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8"><span class="badge bg-secondary">{{ ucfirst($delivery->status) }}</span></dd>
                        <dt class="col-sm-4">Last Updated</dt>
                        <dd class="col-sm-8">{{ $delivery->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                    <form action="{{ route('supplier.deliveries.updateStatus', $delivery) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Update Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" @if($delivery->status=='pending') selected @endif>Pending</option>
                                <option value="in_transit" @if($delivery->status=='in_transit') selected @endif>In Transit</option>
                                <option value="delivered" @if($delivery->status=='delivered') selected @endif>Delivered</option>
                                <option value="failed" @if($delivery->status=='failed') selected @endif>Failed</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('supplier.deliveries.index') }}" class="btn btn-secondary me-2">Back</a>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 