<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\RetailStore;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show retailer dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Check if user is actually a retailer
        if (!$user->hasRole('retailer')) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Retailer role required.');
        }
        
        $retailStore = $user->managedRetailStore;

        if (!$retailStore) {
            return redirect()->route('retailer.profile.create')->with('error', 'Please complete your retail store profile first.');
        }

        // Statistics
        $stats = [
            'total_orders' => Order::where('retail_store_id', $retailStore->id)->count(),
            'pending_orders' => Order::where('retail_store_id', $retailStore->id)->where('status', 'pending')->count(),
            'total_revenue' => Order::where('retail_store_id', $retailStore->id)->where('status', 'delivered')->sum('total_amount'),
            'low_stock_products' => Inventory::where('retail_store_id', $retailStore->id)->where('quantity', '<', 10)->count(),
            'products_count' => Inventory::where('retail_store_id', $retailStore->id)->count(),
            'monthly_orders' => Order::where('retail_store_id', $retailStore->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        // Recent orders
        $recentOrders = Order::where('retail_store_id', $retailStore->id)
            ->with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Low stock items
        $lowStockItems = Inventory::where('retail_store_id', $retailStore->id)
            ->where('quantity', '<', 10)
            ->with('product')
            ->limit(5)
            ->get();

        return view('retailer.dashboard', compact('stats', 'recentOrders', 'lowStockItems', 'retailStore'));
    }

    /**
     * Show orders management
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $query = Order::where('retail_store_id', $retailStore->id)
            ->with(['user', 'orderItems.product']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15);

        return view('retailer.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $order = Order::where('retail_store_id', $retailStore->id)
            ->with(['user', 'orderItems.product', 'deliveries'])
            ->findOrFail($id);

        return view('retailer.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $order = Order::where('retail_store_id', $retailStore->id)->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // Update timestamps based on status
        switch ($request->status) {
            case 'confirmed':
                $order->update(['confirmed_at' => now()]);
                break;
            case 'shipped':
                $order->update(['shipped_at' => now()]);
                break;
            case 'delivered':
                $order->update(['delivered_at' => now()]);
                break;
            case 'cancelled':
                $order->update(['cancelled_at' => now()]);
                break;
        }

        return redirect()->route('retailer.orders.show', $order->id)->with('success', 'Order status updated successfully!');
    }

    /**
     * Show inventory management
     */
    public function inventory(Request $request)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $query = Inventory::where('retail_store_id', $retailStore->id)
            ->with('product');

        // Filter by stock level
        if ($request->stock_level) {
            switch ($request->stock_level) {
                case 'low':
                    $query->where('quantity', '<', 10);
                    break;
                case 'out':
                    $query->where('quantity', 0);
                    break;
                case 'available':
                    $query->where('quantity', '>', 0);
                    break;
            }
        }

        $inventory = $query->paginate(15);

        return view('retailer.inventory.index', compact('inventory'));
    }

    /**
     * Update inventory quantity
     */
    public function updateInventory(Request $request, $id)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $inventory = Inventory::where('retail_store_id', $retailStore->id)->findOrFail($id);
        
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $inventory->update([
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
        ]);

        return redirect()->route('retailer.inventory')->with('success', 'Inventory updated successfully!');
    }

    /**
     * Add new product to inventory
     */
    public function addToInventory(Request $request)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        // Check if product already exists in inventory
        $existing = Inventory::where('retail_store_id', $retailStore->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return redirect()->route('retailer.inventory')->with('error', 'Product already exists in inventory!');
        }

        Inventory::create([
            'retail_store_id' => $retailStore->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
        ]);

        return redirect()->route('retailer.inventory')->with('success', 'Product added to inventory successfully!');
    }

    /**
     * Show returns management
     */
    public function returns(Request $request)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $query = Order::where('retail_store_id', $retailStore->id)
            ->where('status', 'delivered')
            ->with(['user', 'orderItems.product']);

        $returns = $query->latest()->paginate(15);

        return view('retailer.returns.index', compact('returns'));
    }

    /**
     * Process return
     */
    public function processReturn(Request $request, $orderId)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $order = Order::where('retail_store_id', $retailStore->id)->findOrFail($orderId);
        
        $request->validate([
            'return_reason' => 'required|string',
            'refund_amount' => 'required|numeric|min:0|max:' . $order->total_amount,
            'return_items' => 'required|array',
        ]);

        // Update order status to returned
        $order->update([
            'status' => 'returned',
            'notes' => 'Return processed: ' . $request->return_reason,
        ]);

        // Restock returned items
        foreach ($request->return_items as $itemId => $quantity) {
            $orderItem = OrderItem::where('order_id', $order->id)
                ->where('id', $itemId)
                ->first();

            if ($orderItem && $quantity > 0) {
                $inventory = Inventory::where('retail_store_id', $retailStore->id)
                    ->where('product_id', $orderItem->product_id)
                    ->first();

                if ($inventory) {
                    $inventory->increment('quantity', $quantity);
                }
            }
        }

        return redirect()->route('retailer.returns')->with('success', 'Return processed successfully!');
    }

    /**
     * Show analytics/reports
     */
    public function analytics()
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        // Monthly sales data
        $monthlySales = Order::where('retail_store_id', $retailStore->id)
            ->where('status', 'delivered')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->get();

        // Top selling products
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.retail_store_id', $retailStore->id)
            ->where('orders.status', 'delivered')
            ->selectRaw('products.name, SUM(order_items.quantity) as total_sold')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();

        // Order status distribution
        $orderStatuses = Order::where('retail_store_id', $retailStore->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('retailer.analytics', compact('monthlySales', 'topProducts', 'orderStatuses'));
    }

    /**
     * Show profile management
     */
    public function profile()
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        return view('retailer.profile.index', compact('user', 'retailStore'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));
        
        if ($retailStore) {
            $retailStore->update([
                'name' => $request->store_name,
                'address' => $request->store_address,
                'phone' => $request->store_phone,
            ]);
        }

        return redirect()->route('retailer.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show retailer profile creation form
     */
    public function createProfile()
    {
        $user = auth()->user();
        
        // Check if user already has a retail store
        if ($user->managedRetailStore) {
            return redirect()->route('retailer.dashboard')->with('info', 'Your retail store profile is already set up.');
        }
        
        return view('retailer.profile.create');
    }

    /**
     * Store retailer profile
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'opening_time' => 'required|date_format:H:i:s',
            'closing_time' => 'required|date_format:H:i:s|after:opening_time',
        ]);

        $user = auth()->user();

        // Create retail store
        $retailStore = \App\Models\RetailStore::create([
            'name' => $request->store_name,
            'address' => $request->address,
            'manager_id' => $user->id,
            'contact_person' => $user->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'status' => 'active',
        ]);

        return redirect()->route('retailer.dashboard')->with('success', 'Retail store profile created successfully!');
    }
}
