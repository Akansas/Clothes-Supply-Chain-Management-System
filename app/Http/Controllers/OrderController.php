<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Delivery;
use App\Models\DeliveryPartner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of orders based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['orderItems.product', 'retailStore']);

        // Filter orders based on user role
        switch ($user->role->name) {
            case 'manufacturer':
                $query->whereHas('orderItems.product', function ($q) use ($user) {
                    $q->where('manufacturer_id', $user->id);
                });
                break;
            case 'retailer':
                $query->where('retail_store_id', $user->retailStore->id ?? 0);
                break;
            case 'customer':
                $query->where('customer_id', $user->id);
                break;
            case 'vendor':
                $query->whereHas('orderItems.product', function ($q) use ($user) {
                    $q->where('vendor_id', $user->vendor->id ?? 0);
                });
                break;
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $products = Product::active()->get();
        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Calculate total
            $total = 0;
            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
                $total += $product->price * $item['quantity'];
            }

            // Create order
            $firstProduct = Product::find($request->products[0]['product_id']);
            $supplierId = $firstProduct->supplier_id ?? null;
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'customer_id' => Auth::user()->id,
                'retail_store_id' => Auth::user()->retailStore->id ?? null,
                'status' => 'pending',
                'total_amount' => $total,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
                'order_date' => now(),
                'supplier_id' => $supplierId,
            ]);

            // Create order items
            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $item['quantity'],
                ]);

                // Update inventory
                $inventory = Inventory::where('product_id', $item['product_id'])->first();
                if ($inventory) {
                    $inventory->decrement('quantity', $item['quantity']);
                }
            }

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = Order::with(['orderItems.product', 'customer', 'retailStore'])->findOrFail($id);
        $delivery = Delivery::where('order_id', $id)->first();
        
        return view('orders.show', compact('order', 'delivery'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        $products = Product::active()->get();
        $customers = User::whereHas('role', function ($q) {
            $q->where('name', 'customer');
        })->get();
        
        return view('orders.edit', compact('order', 'products', 'customers'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $order->update($request->only(['status', 'shipping_address', 'notes']));

        // If status changed to shipped, create delivery record
        if ($request->status === 'shipped' && $order->wasChanged('status')) {
            Delivery::create([
                'order_id' => $order->id,
                'tracking_number' => 'DEL-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'status' => 'pending',
                'delivery_fee' => 5.00,
            ]);
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order updated successfully!');
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'new_status' => $request->status
        ]);
    }

    /**
     * Process order return
     */
    public function processReturn(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'return_reason' => 'required|string',
            'return_quantity' => 'required|integer|min:1'
        ]);

        // Update order status
        $order->update(['status' => 'returned']);

        // Restore inventory
        foreach ($order->orderItems as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)->first();
            if ($inventory) {
                $inventory->increment('quantity', $request->return_quantity);
            }
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Return processed successfully!');
    }

    /**
     * Display order analytics
     */
    public function analytics()
    {
        $user = Auth::user();
        $query = Order::query();

        // Filter based on user role
        switch ($user->role->name) {
            case 'manufacturer':
                $query->whereHas('orderItems.product', function ($q) use ($user) {
                    $q->where('manufacturer_id', $user->id);
                });
                break;
            case 'retailer':
                $query->where('retail_store_id', $user->retailStore->id ?? 0);
                break;
        }

        // Analytics data
        $totalOrders = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Orders by status
        $ordersByStatus = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly orders
        $monthlyOrders = $query->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count'),
            DB::raw('sum(total_amount) as revenue')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('orders.analytics', compact(
            'totalOrders',
            'totalRevenue', 
            'avgOrderValue',
            'ordersByStatus',
            'monthlyOrders'
        ));
    }

    /**
     * Destroy the specified order
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        // Only allow deletion of pending orders
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be deleted.');
        }

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully!');
    }
}
