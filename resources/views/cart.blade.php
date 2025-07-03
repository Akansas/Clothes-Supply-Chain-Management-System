@extends('layouts.app')

@php
    $activePage = 'cart';
@endphp

@section('content')
<div class="container mx-auto px-4 mt-6">
    <h2 class="text-2xl font-bold mb-4">🛒 Your Shopping Cart</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(count($cart) > 0)
        <table class="w-full table-auto border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Image</th>
                    <th class="px-4 py-2">Product</th>
                    <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Price</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($cart as $id => $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <tr class="text-center border-t">
                        <td class="py-2">
                            <img src="{{ asset('images/' . ($item['image'] ?? 'placeholder.jpg')) }}" class="w-16 h-16 object-cover mx-auto">
                        </td>
                        <td>{{ $item['name'] }}</td>
                        <td>
                            <div class="flex items-center justify-center space-x-2">
                                <form action="{{ route('cart.update', [$id, 'decrease']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-gray-200 px-2 py-1 rounded">−</button>
                                </form>

                                <span class="px-2">{{ $item['quantity'] }}</span>

                                <form action="{{ route('cart.update', [$id, 'increase']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-gray-200 px-2 py-1 rounded">+</button>
                                </form>
                            </div>
                        </td>
                        <td>UgX {{ number_format($item['price']) }}</td>
                        <td>
                            <a href="{{ route('cart.remove', $id) }}" class="text-red-500 hover:underline">Remove</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('order.confirm') }}" method="POST" class="mt-4">
    @csrf
    <a href="{{ route('checkout') }}" class="btn btn-primary">
    Proceed to Checkout
</a>
</form>
    @else
        <p class="text-gray-600">Your cart is empty.</p>
    @endif
</div>
@endsection
