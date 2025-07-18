@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4 text-center">Orders Placed to Manufacturers</h2>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @include('retailer.orders._orders-table', ['orders' => $orders])
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 