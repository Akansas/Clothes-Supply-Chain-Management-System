<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function show()
    {
        return view('checkout');
    }
    public function submit(Request $request)
{
    // Validate input
    $request->validate([
        'address' => 'required|string|max:255',
        'payment_method' => 'required|in:cash_on_delivery,mobile_money',
    ]);

    // You could store order data here
    // For now, we just return a success message
    return redirect()->route('cart')->with('success', 'Order placed successfully!');
}
}
