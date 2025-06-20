@extends('layouts.app', ['activePage' => 'retailer_recommendations', 'title' => 'Recommendations', 'navName' => 'Recommendations', 'activeButton' => 'retailer'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Restock Suggestions (Low Stock)</h4>
                </div>
                <div class="card-body">
                    @if($lowStock->count())
                        <ul>
                            @foreach($lowStock as $product)
                                <li>{{ $product->name }} (SKU: {{ $product->sku }}) - Stock: {{ $product->stock }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>All products are sufficiently stocked.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Promote Best-Sellers</h4>
                </div>
                <div class="card-body">
                    @if($bestSellers->count())
                        <ul>
                            @foreach($bestSellers as $product)
                                <li>{{ $product->name }} (SKU: {{ $product->sku }}) - Sold: {{ $product->total_sales ?? 0 }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No sales data available yet.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Slow-Moving Products (No Sales in Last 30 Days)</h4>
                </div>
                <div class="card-body">
                    @if($slowMoving->count())
                        <ul>
                            @foreach($slowMoving as $product)
                                <li>{{ $product->name }} (SKU: {{ $product->sku }}) - Stock: {{ $product->stock }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No slow-moving products at this time.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 