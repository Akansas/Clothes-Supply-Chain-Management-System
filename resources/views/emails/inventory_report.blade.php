<h2>Warehouse Inventory Report</h2>

@foreach($inventory as $item)
    <p>
        <strong>Product:</strong> {{ $item->product->name }}<br>
        <strong>Quantity:</strong> {{ $item->quantity }}<br>
        <strong>Last Updated:</strong> {{ $item->updated_at->format('Y-m-d') }}
    </p>
    <hr>
@endforeach