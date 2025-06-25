@extends('layouts.app')

@php($title = 'Customer Dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Welcome, {{ Auth::user()->name }}
    </h2>
@endsection

@section('content')
    <!-- Summary Tiles -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Total Orders</h3>
            <p class="text-2xl font-bold mt-2">12</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Pending Shipments</h3>
            <p class="text-2xl font-bold mt-2">3</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Total Spent</h3>
            <p class="text-2xl font-bold mt-2">$1,250</p>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Orders</h3>
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
                <tr class="border-t">
                    <td class="py-2">#1024</td>
                    <td class="py-2">
                        <span class="bg-yellow-400 text-white px-2 py-1 rounded text-xs">Pending</span>
                    </td>
                    <td class="py-2">24 Jun 2025</td>
                    <td class="py-2">$250.00</td>
                </tr>
                <tr class="border-t">
                    <td class="py-2">#1023</td>
                    <td class="py-2">
                        <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Shipped</span>
                    </td>
                    <td class="py-2">22 Jun 2025</td>
                    <td class="py-2">$340.00</td>
                </tr>
                <tr class="border-t">
                    <td class="py-2">#1022</td>
                    <td class="py-2">
                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Cancelled</span>
                    </td>
                    <td class="py-2">20 Jun 2025</td>
                    <td class="py-2">$120.00</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
