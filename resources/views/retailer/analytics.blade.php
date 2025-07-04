@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Retailer Analytics</h2>
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title mb-2">Inventory Overview</h5>
                    <p class="mb-1"><strong>Total Products:</strong> {{ $inventoryStats['total_products'] ?? 0 }}</p>
                    <p><strong>Total Quantity in Stock:</strong> {{ $inventoryStats['total_quantity'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Status Distribution</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-horizontal">
                        @forelse($orderStatuses as $status)
                            <li class="list-group-item flex-fill text-center">
                                <strong>{{ ucfirst($status->status) }}</strong><br>
                                {{ $status->count }} orders
                            </li>
                        @empty
                            <li class="list-group-item text-center">No status data</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 