@extends('layouts.app')
@section('content')
<div class="container py-5">
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('manufacturer.dashboard') }}">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('workforce.index') }}">Workforce Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('manufacturer.inventory.index') }}">Inventory Management</a>
        </li>
    </ul>
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Welcome, {{ $manufacturer->name ?? auth()->user()->name }}</h2>
            <p class="lead text-muted">Manage production orders, quality checks, and manufacturing processes</p>
        </div>
    </div>

    <!-- Manufacturer Chat Widget -->
    <div class="row mb-4">
        <div class="col-12">
            <manufacturer-chat :user-id="{{ auth()->id() }}"></manufacturer-chat>
        </div>
    </div>

    <!-- Action Buttons -->
    {{-- Removed standalone View & Order Raw Materials button and moved to Quick Actions --}}

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Purchase Orders Card -->
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shopping-cart me-2"></i>Purchase Orders</h5>
                    <ul class="list-unstyled mb-0">
                        <li><span class="badge bg-warning me-1">Pending</span> {{ $purchaseOrdersStats['pending'] ?? 0 }}</li>
                        <li><span class="badge bg-primary me-1">Approved</span> {{ $purchaseOrdersStats['approved'] ?? 0 }}</li>
                        <li><span class="badge bg-danger me-1">Rejected</span> {{ $purchaseOrdersStats['rejected'] ?? 0 }}</li>
                        <li><span class="badge bg-success me-1">Delivered</span> {{ $purchaseOrdersStats['delivered'] ?? 0 }}</li>
                        <li><span class="badge bg-secondary me-1">Cancelled</span> {{ $purchaseOrdersStats['cancelled'] ?? 0 }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Retailer Orders Card -->
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-store me-2"></i>Retailer Orders</h5>
                    <ul class="list-unstyled mb-0">
                        <li><span class="badge bg-warning me-1">Pending</span> {{ $retailerOrdersStats['pending'] ?? 0 }}</li>
                        <li><span class="badge bg-primary me-1">Approved</span> {{ $retailerOrdersStats['approved'] ?? 0 }}</li>
                        <li><span class="badge bg-danger me-1">Rejected</span> {{ $retailerOrdersStats['rejected'] ?? 0 }}</li>
                        <li><span class="badge bg-success me-1">Delivered</span> {{ $retailerOrdersStats['delivered'] ?? 0 }}</li>
                        <li><span class="badge bg-secondary me-1">Cancelled</span> {{ $retailerOrdersStats['cancelled'] ?? 0 }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Total Cost Card -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-coins me-2"></i>Total Cost</h5>
                    <h3 class="mb-0 text-danger">${{ number_format($totalCost, 2) }}</h3>
                </div>
            </div>
        </div>
        <!-- Total Revenue Card -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-cash-register me-2"></i>Total Revenue</h5>
                    <h3 class="mb-0 text-success">${{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Removed Workforce Distribution Management card/section as per user request --}}
    @php
        $supplyCenterJson = json_encode($charts['workers_by_supply_center'] ?? (object)[]);
        $shiftJson = json_encode($charts['workers_by_shift'] ?? (object)[]);
    @endphp
    <script id="supply-center-data" type="application/json">{!! $supplyCenterJson !!}</script>
    <script id="shift-data" type="application/json">{!! $shiftJson !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const workersBySupplyCenter = JSON.parse(document.getElementById('supply-center-data').textContent);
        const workersByShift = JSON.parse(document.getElementById('shift-data').textContent);
        if (document.getElementById('workersBySupplyCenterChart')) {
            new Chart(document.getElementById('workersBySupplyCenterChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: workersBySupplyCenter.labels,
                    datasets: [{
                        label: 'Workers',
                        data: workersBySupplyCenter.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    }]
                },
                options: {responsive: true}
            });
        }
        if (document.getElementById('workersByShiftChart')) {
            new Chart(document.getElementById('workersByShiftChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: workersByShift.labels,
                    datasets: [{
                        label: 'Workers',
                        data: workersByShift.data,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)'
                    }]
                },
                options: {responsive: true}
            });
        }
    </script>

    <!-- Remove the entire Manufacturing Analytics section and its components/cards -->

        <!-- Finished Products Table -->
    {{-- Removed Finished Products (Clothes) table from dashboard. See inventory page for this table. --}}

    <!-- Raw Materials Table -->
    {{-- Removed Raw Materials (Supplier Products) table as per user request --}}

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.materials.browse') }}" class="btn btn-primary w-100">
                                <i class="fas fa-shopping-cart me-2"></i> View & Order Raw Materials
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.purchase-orders') }}" class="btn btn-warning w-100">
                                <i class="fas fa-shopping-basket me-2"></i>My Purchase Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.retailer-orders') }}" class="btn btn-primary w-100">
                                <i class="fas fa-list me-2"></i>Retailer Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.analytics') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-bar me-2"></i>Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchase Orders Table -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Purchase Orders</h5>
                    <a href="{{ route('manufacturer.purchase-orders') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if(isset($recentPurchaseOrders) && $recentPurchaseOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Supplier</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPurchaseOrders as $order)
                                    @php
                                        $badgeTextColor = $order->getStatusBadgeClass() === 'badge-warning' ? '#212529' : '#fff';
                                        $badgeStyle = 'font-size: 1.05em; font-weight: bold; padding: 0.6em 1.2em; box-shadow: 0 1px 4px rgba(0,0,0,0.08); color: ' . $badgeTextColor . ';';
                                    @endphp
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>
                                            @if($order->supplier)
                                                {{ $order->supplier->company_name }}
                                                @if($order->supplier->user)
                                                    ({{ $order->supplier->user->name }})
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $order->getStatusBadgeClass() }}" style="font-size: 1em; font-weight: 600; padding: 0.4em 1em; border-radius: 1em; color: #111; background: #fff;">
                                                {{ $order->getStatusText() ?: strtoupper($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            @if(in_array($order->status, ['pending', 'confirmed']))
                                                <a href="{{ route('manufacturer.purchase-orders.edit', $order) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('manufacturer.purchase-orders.cancel', $order) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                            <a href="{{ route('manufacturer.purchase-orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent purchase orders found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(isset($conversations))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Supplier Chat</h5>
                </div>
                <div class="card-body">
                    @foreach($conversations as $conversation)
                        <h6>Chat with:
                            @foreach($conversation->participants as $participant)
                                @if($participant->id !== $user->id)
                                    {{ $participant->name }}
                                @endif
                            @endforeach
                        </h6>
                        <div>
                            @php
                                $recentMessages = $conversation->messages->sortByDesc('created_at')->take(3)->reverse();
                            @endphp
                            @foreach($recentMessages as $message)
                                <div>
                                    <strong>{{ $message->user->name }}:</strong>
                                    {{ $message->body }}
                                    <span class="text-muted small">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('chat.show', $conversation) }}" class="btn btn-outline-primary btn-sm mt-2 mb-2">
                            Go to Chat
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 