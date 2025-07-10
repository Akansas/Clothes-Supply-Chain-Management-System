<h2>Daily Delivery Report</h2>

@foreach($shipments as $shipment)
    <p>
        <strong>Shipment ID:</strong> {{ $shipment->id }}<br>
        <strong>Destination:</strong> {{ $shipment->destination }}<br>
        <strong>Status:</strong> {{ $shipment->status }}
    </p>
    <hr>
@endforeach