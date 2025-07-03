@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-2xl font-bold mb-4">📦 Track Your Order</h2>
    <form method="GET" action="">
        <div class="mb-3">
            <label for="order_id" class="form-label">Enter Order ID:</label>
            <input type="text" id="order_id" name="order_id" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Track</button>
    </form>

    {{-- Add logic to show tracking results if order_id is entered --}}
</div>
@endsection