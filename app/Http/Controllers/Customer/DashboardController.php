<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;
use App\Models\Vendor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->with(['orderItems.product', 'deliveries'])->latest()->take(5)->get();
        $recentDeliveries = Delivery::whereIn('order_id', $user->orders()->pluck('id'))->with(['order', 'driver'])->latest()->take(3)->get();
        
        // Statistics
        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->where('status', 'pending')->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();
        $totalSpent = $user->orders()->where('status', 'completed')->sum('total_amount');
        
        return view('customer.dashboard', compact('user', 'orders', 'recentDeliveries', 'totalOrders', 'pendingOrders', 'completedOrders', 'totalSpent'));
    }

    /**
     * Browse products
     */
    public function browseProducts(Request $request)
    {
        $query = Product::with(['vendor', 'design.collection'])->where('is_active', true);
        
        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        $products = $query->paginate(12);
        $vendors = Vendor::all();
        
        return view('customer.products.browse', compact('products', 'vendors'));
    }

    /**
     * Show product details
     */
    public function showProduct($id)
    {
        $product = Product::with(['vendor', 'design.collection', 'inventory'])->findOrFail($id);
        $relatedProducts = Product::where('vendor_id', $product->vendor_id)
                                 ->where('id', '!=', $product->id)
                                 ->where('is_active', true)
                                 ->take(4)
                                 ->get();
        
        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Show shopping cart
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity
                ];
                $total += $product->price * $quantity;
            }
        }
        
        return view('customer.cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add to cart
     */
    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $request->quantity ?? 1;
        } else {
            $cart[$productId] = $request->quantity ?? 1;
        }
        
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Remove from cart
     */
    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        
        return redirect()->route('customer.cart')->with('success', 'Product removed from cart!');
    }

    /**
     * Checkout
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.products.browse')->with('error', 'Your cart is empty!');
        }
        
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity
                ];
                $total += $product->price * $quantity;
            }
        }
        
        return view('customer.checkout.index', compact('cartItems', 'total'));
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'payment_method' => 'required|string',
        ]);
        
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.products.browse')->with('error', 'Your cart is empty!');
        }
        
        $user = auth()->user();
        $total = 0;
        
        // Calculate total
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }
        
        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'customer_id' => $user->id,
            'order_number' => 'ORD-' . time(),
            'source' => 'customer',
            'total_amount' => $total,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'shipping_city' => 'Kampala',
            'shipping_state' => 'Central',
            'shipping_zip' => '00000',
            'shipping_country' => 'Uganda',
            'billing_address' => $request->billing_address,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'order_date' => now(),
        ]);
        
        // Create order items
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $quantity,
                ]);
            }
        }
        
        // Clear cart
        session()->forget('cart');
        
        return redirect()->route('customer.orders.show', $order->id)->with('success', 'Order placed successfully!');
    }

    /**
     * Show orders
     */
    public function orders()
    {
        $user = auth()->user();
        $orders = $user->orders()->with(['orderItems.product', 'deliveries'])->latest()->paginate(10);
        
        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $user = auth()->user();
        $order = $user->orders()->with(['orderItems.product', 'deliveries.driver'])->findOrFail($id);
        
        return view('customer.orders.show', compact('order'));
    }

    /**
     * Track order
     */
    public function trackOrder($id)
    {
        $user = auth()->user();
        $order = $user->orders()->with(['orderItems.product', 'deliveries.driver'])->findOrFail($id);
        
        return view('customer.orders.track', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancelOrder($id)
    {
        $user = auth()->user();
        $order = $user->orders()->findOrFail($id);
        
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            return redirect()->route('customer.orders.show', $order->id)->with('success', 'Order cancelled successfully!');
        }
        
        return redirect()->route('customer.orders.show', $order->id)->with('error', 'Order cannot be cancelled at this stage.');
    }

    /**
     * Show profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('customer.profile.index', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        
        $user->update($request->only(['name', 'email', 'phone', 'address']));
        
        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }
}
