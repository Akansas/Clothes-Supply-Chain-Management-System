@extends('layouts.app')

@php
    $activePage = 'checkout';
    $title = 'Checkout';
    $navName ='Checkout';
@endphp

@section('content')
<div class="container mx-auto px-4 mt-8">
    <h2 class="text-2xl font-bold mb-6 text-center">🧾 Checkout</h2>

    <form action="{{ route('order.confirm') }}" method="POST" class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        @csrf

        {{-- 🏠 Address --}}
        <div class="mb-4">
            <label for="address" class="block text-gray-700 font-semibold mb-2">📍 Delivery Address</label>
            <textarea name="address" id="address" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-orange-200" required></textarea>
        </div>

        {{-- 💳 Payment Method --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">💳 Select Payment Method</label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="payment_method" value="cash_on_delivery" class="mr-2" required>
                    Cash on Delivery
                </label>
                <label class="flex items-center">
                    <input type="radio" name="payment_method" value="mobile_money" class="mr-2">
                    Mobile Money
                </label>
                <label class="flex items-center">
                    <input type="radio" name="payment_method" value="credit_card" class="mr-2">
                    Credit/Debit Card
                </label>
            </div>
        </div>

        {{-- ✅ Confirm --}}
        <div class="text-center mt-6">
            <button type="submit" class="bg-orange-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-orange-700">
                ✅ Confirm Order
            </button>
        </div>
    </form>
</div>
@endsection
