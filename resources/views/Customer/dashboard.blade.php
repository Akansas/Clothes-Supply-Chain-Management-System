@extends('layouts.app')

@php($title = 'Customer Dashboard')
@php($activePage ="Customer Dashboard")
@php($navName ="Customer Dashboard")
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Welcome, {{ Auth::user()->name }}
    </h2>
@endsection

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Welcome {{ Auth::user()->name}}</h1>

    



<div class="container mx-auto px-4" x-data="{ showCatalog: false }">

    {{-- ✅ Button to toggle product catalog --}}
    <button @click="showCatalog = !showCatalog"
    class="mb-6 px-6 py-3 text-lg font-semibold bg-gradient-to-r from-orange-500 to-yellow-500 text-white rounded-xl shadow-lg hover:from-orange-600 hover:to-yellow-600 transition transform hover:scale-105">
    View Product Catalog
</button>

    {{-- ✅ Catalog Section (hidden until clicked) --}}
    <div x-show="showCatalog" x-transition>
    <h2 class="text-xl font-semibold mb-4">Product Catalog</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($products as $product)
            <div class="border rounded-lg shadow-md p-4 text-center">
                <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}"
                    class="w-24 h-24 object-cover mx-auto mb-3 rounded">

                <h2 class="font-semibold text-lg mb-1">{{ $product->name }}</h2>
                <p><strong>Material:</strong> {{ $product->material }}</p>
                <p><strong>Size:</strong> {{ $product->size }}</p>
                <p><strong>Color:</strong> {{ $product->color }}</p>
                <p><strong>Price:</strong> UgX {{ number_format($product->unit_price) }}</p>

                <a href="{{ route('cart.add', $product->id) }}">
                    <button class="bg-orange-500 text-white px-4 py-2 mt-3 rounded hover:bg-orange-600">
                        Add to cart
                    </button>
                </a>
            </div>
        @endforeach
  </div>
</div>
<a href="{{ route('cart.view') }}">
    <button class="mb-6 ml-4 px-6 py-3 text-lg font-semibold bg-green-600 text-white rounded-xl shadow-lg hover:bg-green-700 transition transform hover:scale-105">
        🛒 View Cart
 </button>
</a>
</div>
</div>


    <!-- 🔢 Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">🛒 Total Orders</h3>
            <p class="text-2xl font-bold mt-2">12</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">🚚 Pending Shipments</h3>
            <p class="text-2xl font-bold mt-2">3</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">💳 Total Spent</h3>
            <p class="text-2xl font-bold mt-2">$1,250</p>
        </div>
    </div>

    <!-- ✅ Profile Completion Progress -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <p class="font-semibold mb-2">🔧 Profile Completion</p>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-green-500 h-4 rounded-full w-[70%]"></div>
        </div>
        <p class="text-sm text-gray-600 mt-1">70% completed</p>
    </div>

    <div class="card">
    <div class="card-header">
        <h4 class="card-title">Take Action</h4>
        <p class="card-category">Common tasks at your fingertips</p>
    </div>
    <div class="card-body text-center">
        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-round m-1">Update Profile</a>
        <a href="{{ route('customer.track-order') }}" class="btn btn-warning btn-round m-1">Track Order</a>
        <a href="{{ route('customer.contactSupport') }}" class="btn btn-success btn-round m-1">Contact Support</a>
    </div>
</div>

    <!-- 📣 Notifications -->
    <div class="bg-yellow-100 p-4 rounded-lg mb-6">
        <p class="text-yellow-800 font-semibold text-sm">📣 Reminder:</p>
        <p class="text-yellow-700 text-sm">Your next shipment is expected in 3 days.</p>
    </div>

    <!-- 📊 Placeholder Chart -->
    <div class="bg-white p-6 rounded-lg shadow mb-6 text-center text-gray-400 border border-dashed">
        <p class="text-sm">📈 Your order trends will appear here soon...</p>
    </div>

    <!-- 🧾 Recent Orders Table -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">🧾 Recent Orders</h3>
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-gray-600 border-b">
                    <th class="pb-2">Order #</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Date</th>
                    <th class="pb-2">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-t searchable">
                    <td class="py-2">#1024</td>
                    <td class="py-2"><span class="bg-yellow-400 text-white px-2 py-1 rounded text-xs">Pending</span></td>
                    <td class="py-2">24 Jun 2025</td>
                    <td class="py-2">$250.00</td>
                </tr>
                <tr class="border-t searchable">
                    <td class="py-2">#1023</td>
                    <td class="py-2"><span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Shipped</span></td>
                    <td class="py-2">22 Jun 2025</td>
                    <td class="py-2">$340.00</td>
                </tr>
                <tr class="border-t searchable">
                    <td class="py-2">#1022</td>
                    <td class="py-2"><span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Cancelled</span></td>
                    <td class="py-2">20 Jun 2025</td>
                    <td class="py-2">$120.00</td>
                </tr>
            </tbody>
        </table>
    </div>
   
    

@endsection
