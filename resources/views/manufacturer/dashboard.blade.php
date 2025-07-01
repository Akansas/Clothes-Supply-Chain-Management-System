@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Welcome, {{ $manufacturer->name ?? auth()->user()->name }}</h2>
            <p class="lead text-muted">Manage production orders, quality checks, and manufacturing processes</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('manufacturer.materials.browse') }}" class="btn btn-lg btn-primary">
                <i class="fas fa-shopping-cart me-2"></i> View & Order Raw Materials
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_production_orders'] }}</h4>
                            <small>Total Orders</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-industry fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_production_orders'] }}</h4>
                            <small>Active Orders</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['completed_orders'] }}</h4>
                            <small>Completed</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending_quality_checks'] }}</h4>
                            <small>Pending QC</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Production Orders -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Recent Production Orders</h5>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->product->name ?? 'N/A' }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'in_progress' ? 'warning' : 'info') }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->expected_completion ? $order->expected_completion->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent production orders</p>
                    @endif
                    <a href="{{ route('manufacturer.production-orders') }}" class="btn btn-outline-primary btn-sm">View All Orders</a>
                </div>
            </div>
        </div>

        <!-- Finished Products Table -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Finished Products (Clothes)</h5>
                    <a href="{{ route('manufacturer.products.create') }}" class="btn btn-sm btn-success">Add Product</a>
                </div>
                <div class="card-body">
                    @if(isset($products) && $products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="Image" style="max-width: 60px;">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category ?? '-' }}</td>
                                        <td>{{ $product->description }}</td>
                                        <td>{{ $product->price ? ('$' . number_format($product->price, 2)) : '-' }}</td>
                                        <td>{{ $product->inventory->quantity ?? 0 }}</td>
                                        <td>
                                            <a href="{{ route('manufacturer.products.edit', $product->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('manufacturer.products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No finished products found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Active Production Stages -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Active Production Stages</h5>
                </div>
                <div class="card-body">
                    @if($activeStages->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Stage Name</th>
                                        <th>Product</th>
                                        <th>Order</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeStages as $stage)
                                    <tr>
                                        <td>{{ $stage->name }}</td>
                                        <td>{{ $stage->productionOrder->product->name ?? 'N/A' }}</td>
                                        <td>#{{ $stage->productionOrder->id ?? 'N/A' }}</td>
                                        <td>{{ $stage->duration }} days</td>
                                        <td>
                                            <span class="badge bg-{{ $stage->status === 'completed' ? 'success' : ($stage->status === 'in_progress' ? 'warning' : 'info') }}">
                                                {{ ucfirst(str_replace('_', ' ', $stage->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('manufacturer.production-orders.show', $stage->productionOrder->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No active production stages</p>
                    @endif
                    <a href="{{ route('manufacturer.production-stages') }}" class="btn btn-outline-primary">View All Stages</a>
                </div>
            </div>
        </div>
    </div>

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
                            <a href="{{ route('manufacturer.production-orders.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>New Production Order
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.production-stages') }}" class="btn btn-success w-100">
                                <i class="fas fa-tasks me-2"></i>Manage Stages
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.quality-checks') }}" class="btn btn-warning w-100">
                                <i class="fas fa-clipboard-check me-2"></i>Quality Checks
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