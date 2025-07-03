<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CustomerController extends Controller
{
    public function dashboard(){
        $products = Product::all();
        return view('customer.dashboard', compact('products'));
    }
    public function contactSupport()
{
    return view('customer.contactSupport'); 
}
public function sendSupport(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:1000',
    ]);
 
    return back()->with('success', 'Your message has been sent to support!');
}
public function trackOrder()
{
    return view('customer.track-order', [
        'title' => 'Track Order',
    'activePage' => 'track-order',
    'navName' => 'Customer Dashboard'
]);
}

}
