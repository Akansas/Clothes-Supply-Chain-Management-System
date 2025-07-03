<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $cart = session()->get('cart', []);

    if(isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            "name" => $product->name,
            "price" => $product->unit_price,
            "image" => $product->image,
            "quantity" => 1
        ];
    }

    session()->put('cart', $cart);

    return redirect()->back()->with('success', 'Product added to cart!');
}
public function viewCart()
{
    $cart = session()->get('cart', []);
    $title= 'Your Cart';
    $activePage='cart';
    $navName= 'cart';
    return view('cart', compact('cart', 'title', 'activePage', 'navName'));
}

public function removeFromCart($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        unset($cart[$id]);
        session()->put('cart', $cart);
    }

    return redirect()->route('cart.view')->with('success', 'Product removed from cart.');
}
public function updateQuantity(Request $request, $id, $action)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        if ($action === 'increase') {
            $cart[$id]['quantity']++;
        } elseif ($action === 'decrease' && $cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']--;
        }
        session()->put('cart', $cart);
    }

    return redirect()->back()->with('success', 'Cart updated successfully.');
}
}
