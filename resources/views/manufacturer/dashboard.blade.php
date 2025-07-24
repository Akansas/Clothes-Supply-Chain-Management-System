@extends('layouts.app')
@section('content')
<div class="container py-5">
    {{-- Removed duplicate Chat with Supplier card at the top right corner --}}
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
    <!-- Quick Actions (moved up) -->
    <div class="row mb-4">
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

    <!-- Reports Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Daily Reports</h5>
                    <div>
                        <a href="{{ route('manufacturer.reports.download', ['type' => 'pdf']) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-file-pdf me-1"></i>Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Production Statistics -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success"><i class="fas fa-industry me-2"></i>Production Statistics</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Total Revenue</small>
                                            <div class="fw-bold text-success">${{ number_format($totalRevenue, 2) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Total Cost</small>
                                            <div class="fw-bold text-danger">${{ number_format($totalCost, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <small class="text-muted">Profit Margin</small>
                                            <div class="fw-bold text-info">{{ $totalRevenue > 0 ? number_format((($totalRevenue - $totalCost) / $totalRevenue) * 100, 1) : 0 }}%</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Active Orders</small>
                                            <div class="fw-bold text-warning">{{ ($purchaseOrdersStats['pending'] ?? 0) + ($retailerOrdersStats['pending'] ?? 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary"><i class="fas fa-clipboard-list me-2"></i>Order Summary</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Purchase Orders</small>
                                            <div class="fw-bold">{{ array_sum($purchaseOrdersStats ?? []) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Retailer Orders</small>
                                            <div class="fw-bold">{{ array_sum($retailerOrdersStats ?? []) }}</div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <small class="text-muted">Delivered</small>
                                            <div class="fw-bold text-success">{{ ($purchaseOrdersStats['delivered'] ?? 0) + ($retailerOrdersStats['delivered'] ?? 0) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Pending Approval</small>
                                            <div class="fw-bold text-warning">{{ ($purchaseOrdersStats['pending'] ?? 0) + ($retailerOrdersStats['pending'] ?? 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Breakdown -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-3"><i class="fas fa-list-check me-2"></i>Order Status Breakdown</h6>
                            
                            <!-- Purchase Orders Status Breakdown -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="fas fa-shopping-cart me-2"></i>Purchase Orders (From Suppliers)</h6>
                                    <div class="row">
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded-circle p-2 me-2">
                                                    <i class="fas fa-clock text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Pending</small>
                                                    <div class="fw-bold">{{ $purchaseOrdersStats['pending'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle p-2 me-2">
                                                    <i class="fas fa-thumbs-up text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Approved</small>
                                                    <div class="fw-bold">{{ $purchaseOrdersStats['approved'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle p-2 me-2">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Delivered</small>
                                                    <div class="fw-bold">{{ $purchaseOrdersStats['delivered'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger rounded-circle p-2 me-2">
                                                    <i class="fas fa-times text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Rejected</small>
                                                    <div class="fw-bold">{{ $purchaseOrdersStats['rejected'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary rounded-circle p-2 me-2">
                                                    <i class="fas fa-ban text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Cancelled</small>
                                                    <div class="fw-bold">{{ $purchaseOrdersStats['cancelled'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Retailer Orders Status Breakdown -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-success mb-3"><i class="fas fa-store me-2"></i>Retailer Orders (To Retailers)</h6>
                                    <div class="row">
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded-circle p-2 me-2">
                                                    <i class="fas fa-clock text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Pending</small>
                                                    <div class="fw-bold">{{ $retailerOrdersStats['pending'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle p-2 me-2">
                                                    <i class="fas fa-thumbs-up text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Approved</small>
                                                    <div class="fw-bold">{{ $retailerOrdersStats['approved'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle p-2 me-2">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Delivered</small>
                                                    <div class="fw-bold">{{ $retailerOrdersStats['delivered'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger rounded-circle p-2 me-2">
                                                    <i class="fas fa-times text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Rejected</small>
                                                    <div class="fw-bold">{{ $retailerOrdersStats['rejected'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary rounded-circle p-2 me-2">
                                                    <i class="fas fa-ban text-white"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Cancelled</small>
                                                    <div class="fw-bold">{{ $retailerOrdersStats['cancelled'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <div class="row mt-4">
        <div class="col-12">
            <div class="row justify-content-end">
                <!-- Removed duplicate Chat with Supplier card from here -->
            </div>
        </div>
    </div>
    {{-- Duplicate Quick Actions card removed below --}}
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

<!-- Alpine Dropdown Chat Button with Partners -->
<div x-data="{ open: false }" class="relative inline-block text-left">
    <button @click="open = !open"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        Chat with Partners
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" @click.away="open = false"
         x-transition
         class="absolute z-50 mt-2 w-72 bg-white shadow-lg rounded-lg border border-gray-200">

        <!-- Suppliers -->
        <div class="px-4 py-2 border-b font-semibold text-gray-700">Suppliers</div>
        @forelse ($suppliers as $supplier)
            <a href="{{ route('manufacturer.chat.index', ['partner' => $supplier->id]) }}"
               class="block px-4 py-2 text-sm text-gray-800 hover:bg-blue-100">
                {{ $supplier->name }}
            </a>
        @empty
            <div class="px-4 py-2 text-sm text-gray-400">No suppliers</div>
        @endforelse

        <!-- Retailers -->
        <div class="px-4 py-2 border-t border-b font-semibold text-gray-700">Retailers</div>
        @forelse ($retailers as $retailer)
            <a href="{{ route('manufacturer.chat.index', ['partner' => $retailer->id]) }}"
               class="block px-4 py-2 text-sm text-gray-800 hover:bg-blue-100">
                {{ $retailer->name }}
            </a>
        @empty
            <div class="px-4 py-2 text-sm text-gray-400">No retailers</div>
        @endforelse
    </div>
</div>

    
   <!-- @if(isset($conversations))
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
    @endif-->
</div>

<!-- Floating Chat with Supplier Card -->
<<<<<<< HEAD
<!--<div class="floating-chat-card" style="position: fixed; bottom: 30px; right: 30px; z-index: 1050; width: 70px; padding: 0; background: none; border: none; box-shadow: none;">
=======
<div class="floating-chat-card" style="position: fixed; bottom: 30px; right: 30px; z-index: 1050; width: 70px; padding: 0; background: none; border: none; box-shadow: none;">
>>>>>>> 6f55937ec84d76cf83ff2e5a4cd98cbd63576ba5
    <div class="card text-center shadow-sm" style="border-radius: 50%; background: #007bff; color: #fff; padding: 0.5rem 0.5rem; border: none;">
        <div class="card-body p-2 d-flex flex-column align-items-center" style="padding: 0.5rem 0.5rem; background: transparent;">
            <i class="fas fa-comments mb-1" style="font-size: 2em; color: #fff;"></i>
            <a href="{{ route('chat.index') }}" class="btn btn-sm mt-1" style="background: #fff; color: #007bff; font-size: 0.85em; padding: 2px 10px; border-radius: 12px; border: none;">Chat</a>
        </div>
    </div>
<<<<<<< HEAD
</div>-->

<!-- Floating New Message Button -->
<!--<button type="button" class="btn btn-primary rounded-circle floating-chat-btn" data-bs-toggle="modal" data-bs-target="#newMessageModal" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; font-size: 1.5em; z-index: 9999; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
    <i class="fas fa-plus"></i>
</button>-->

<!-- New Message Modal -->
<!--<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
=======
</div>

<!-- Floating New Message Button -->
<button type="button" class="btn btn-primary rounded-circle floating-chat-btn" data-bs-toggle="modal" data-bs-target="#newMessageModal" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; font-size: 1.5em; z-index: 9999; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
    <i class="fas fa-plus"></i>
</button>

<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
>>>>>>> 6f55937ec84d76cf83ff2e5a4cd98cbd63576ba5
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newMessageModalLabel">Start New Conversation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="startChatForm" method="GET">
        <div class="modal-body">
          <label for="contactSelect" class="form-label">Select Contact</label>
          <select class="form-select" id="contactSelect" name="user_id" required>
<<<<<<< HEAD
            <option value="">Choose...</option>-->
            @php
               // $user = auth()->user();
               // if ($user->hasRole('manufacturer')) {
                //    $contacts = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'raw_material_supplier'); })->where('id', '!=', $user->id)->get();
                //} elseif ($user->hasRole('raw_material_supplier')) {
                //    $contacts = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'manufacturer'); })->where('id', '!=', $user->id)->get();
                //} else {
                //    $contacts = collect();
               // }
            @endphp
            {{--@foreach($contacts as $contact)
=======
            <option value="">Choose...</option>
            @php
                $user = auth()->user();
                if ($user->hasRole('manufacturer')) {
                    $contacts = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'raw_material_supplier'); })->where('id', '!=', $user->id)->get();
                } elseif ($user->hasRole('raw_material_supplier')) {
                    $contacts = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'manufacturer'); })->where('id', '!=', $user->id)->get();
                } else {
                    $contacts = collect();
                }
            @endphp
            @foreach($contacts as $contact)
>>>>>>> 6f55937ec84d76cf83ff2e5a4cd98cbd63576ba5
                <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->role->name }})</option>
            @endforeach
          </select>
        </div>
<<<<<<< HEAD
        <!--<div class="modal-footer">
=======
        <div class="modal-footer">
>>>>>>> 6f55937ec84d76cf83ff2e5a4cd98cbd63576ba5
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Start Chat</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('startChatForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var userId = document.getElementById('contactSelect').value;
        if (userId) {
            window.location.href = '/chat/with/' + userId;
        }
    });
});
<<<<<<< HEAD
</script>-->
<!-- End Floating Chat Button/Modal -->--}}

=======
</script>
<!-- End Floating Chat Button/Modal -->
>>>>>>> 6f55937ec84d76cf83ff2e5a4cd98cbd63576ba5
@endsection 