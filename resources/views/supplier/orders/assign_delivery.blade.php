@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Assign Delivery Personnel & Ship Order</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('supplier.orders.ship', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="driver_id" class="form-label">Select Delivery Personnel</label>
                            <select name="driver_id" id="driver_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach($deliveryPersonnel as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }} ({{ $person->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        @if(count($deliveryPersonnel) === 0)
                            <div class="alert alert-warning">No delivery personnel available. Please add delivery personnel before shipping.</div>
                        @endif
                        <button type="submit" class="btn btn-success" @if(count($deliveryPersonnel) === 0) disabled @endif>Ship Order</button>
                        <a href="{{ route('supplier.orders.show', $order) }}" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 