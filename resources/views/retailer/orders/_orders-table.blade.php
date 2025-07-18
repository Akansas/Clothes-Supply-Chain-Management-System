<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Manufacturer</th>
            <th>Product(s)</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ optional($order->product->manufacturer)->name ?? 'N/A' }}</td>
                <td>{{ $order->product->name ?? 'N/A' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>
                    <span class="badge bg-{{ $order->getStatusBadgeClass() }} text-dark fw-bold">
                        {{ $order->getStatusText() }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('retailer.orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('retailer.orders.update-status', $order->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No orders found</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="d-flex justify-content-center mt-4">
    {{ $orders->links() }}
</div> 