<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\RetailStore;
use App\Models\User;
use App\Models\Role;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;

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
            'total_orders' => Order::where('retailer_id', $user->id)->count(),
            'pending_orders' => Order::where('retailer_id', $user->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('retailer_id', $user->id)->where('status', 'delivered')->count(),
            'approved_orders' => Order::where('retailer_id', $user->id)->where('status', 'approved')->count(),
            'cancelled_orders' => Order::where('retailer_id', $user->id)->where('status', 'cancelled')->count(),
            'rejected_orders' => Order::where('retailer_id', $user->id)->where('status', 'rejected')->count(),
            'total_cost' => Order::where('retailer_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'low_stock_products' => Inventory::where('retail_store_id', $retailStore->id)->where('quantity', '<', 10)->count(),
            'products_count' => Inventory::where('retail_store_id', $retailStore->id)->count(),
            'monthly_orders' => Order::where('retailer_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        // Inventory collection
        $inventory = Inventory::where('retail_store_id', $retailStore->id)->with('product')->get();
        // Low stock items using min_stock_level or 10
        $lowStockItems = $inventory->filter(function($item) {
            $minStock = $item->product->min_stock_level ?? 10;
            return $item->quantity < $minStock;
        })->take(5);
        $stats['low_stock_products'] = $lowStockItems->count();

        // Recent orders
        $recentOrders = Order::where('retailer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

         // Fetch all manufacturers
         $manufacturerRole = Role::where('name', 'manufacturer')->first();
         $manufacturers = $manufacturerRole
          ? User::where('role_id', $manufacturerRole->id)->get()
          : collect();
   

        return view('retailer.dashboard', compact(
            'stats',
            'recentOrders',
            'lowStockItems',
            'retailStore',
            'manufacturers'
        ));
    }

    /**
     * Show orders management
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
        $orders = \App\Models\Order::with(['product.manufacturer'])
            ->where('retailer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('retailer.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $user = auth()->user();
        // Try to find by retailer_id first (production orders)
        $order = \App\Models\Order::where('retailer_id', $user->id)
            ->with(['user', 'orderItems.product', 'deliveries'])
            ->find($id);
        // If not found, try by retail_store_id (store orders)
        if (!$order && $user->managedRetailStore) {
            $order = \App\Models\Order::where('retail_store_id', $user->managedRetailStore->id)
                ->with(['user', 'orderItems.product', 'deliveries'])
                ->find($id);
        }
        if (!$order) {
            abort(404, 'Order not found');
        }
        return view('retailer.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $user = auth()->user();
        // Try to find by retailer_id first (production orders)
        $order = \App\Models\Order::where('retailer_id', $user->id)->find($id);
        // If not found, try by retail_store_id (store orders)
        if (!$order && $user->managedRetailStore) {
            $order = \App\Models\Order::where('retail_store_id', $user->managedRetailStore->id)->find($id);
        }
        if (!$order) {
            abort(404, 'Order not found');
        }
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);
        $order->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        // If cancelling, also update production_orders
        if ($request->status === 'cancelled') {
            \App\Models\ProductionOrder::where('order_number', $order->order_number)
                ->update(['status' => 'cancelled']);
        }
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
        // Debug output
        \Log::info('Retailer inventory:', $inventory->toArray());

        return view('retailer.inventory.index', compact('inventory'));
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
     * Show analytics/reports
     */
    public function analytics()
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;

        if (!$retailStore) {
            return redirect()->route('retailer.profile.create')->with('error', 'Please complete your retail store profile first.');
        }

        // Order Analytics
        $orderAnalytics = [
            'total_orders' => Order::where('retailer_id', $user->id)->count(),
            'orders_by_status' => Order::where('retailer_id', $user->id)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'monthly_orders' => Order::where('retailer_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_revenue' => Order::where('retailer_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'avg_order_value' => Order::where('retailer_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->avg('total_amount') ?? 0,
            'recent_orders' => Order::where('retailer_id', $user->id)
                ->with(['orderItems.product'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        // Inventory Analytics (use min_stock_level logic for low stock)
        $inventory = Inventory::where('retail_store_id', $retailStore->id)->with('product')->get();
        $lowStockItems = $inventory->filter(function($item) {
            $minStock = $item->product->min_stock_level ?? 10;
            return $item->quantity < $minStock;
        });
        $inventoryAnalytics = [
            'total_products' => $inventory->count(),
            'low_stock_products' => $lowStockItems->count(),
            'out_of_stock' => $inventory->where('quantity', 0)->count(),
            'total_inventory_value' => $inventory->sum(function($item) {
                return ($item->quantity * ($item->product->price ?? 0));
            }),
            'top_products' => $inventory->sortByDesc('quantity')->take(5),
            'low_stock_items' => $lowStockItems->take(5),
        ];

        // Performance Metrics
        $performanceMetrics = [
            'order_completion_rate' => $orderAnalytics['total_orders'] > 0 
                ? round((($orderAnalytics['total_orders'] - ($orderAnalytics['orders_by_status']['cancelled'] ?? 0)) / $orderAnalytics['total_orders']) * 100, 1)
                : 0,
            'avg_delivery_time' => Order::where('retailer_id', $user->id)
                ->whereNotNull('delivered_at')
                ->whereNotNull('created_at')
                ->get()
                ->avg(function($order) {
                    return $order->created_at->diffInDays($order->delivered_at);
                }) ?? 0,
            'customer_satisfaction' => 85, // Placeholder - could be calculated from reviews/ratings
        ];

        // Monthly Trends
        $monthlyTrends = Order::where('retailer_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Add debugging information
        \Log::info('Retailer Analytics Data:', [
            'user_id' => $user->id,
            'retail_store_id' => $retailStore->id,
            'order_analytics' => $orderAnalytics,
            'inventory_analytics' => $inventoryAnalytics,
            'performance_metrics' => $performanceMetrics,
            'monthly_trends' => $monthlyTrends->toArray(),
        ]);

        return view('retailer.analytics', compact(
            'orderAnalytics',
            'inventoryAnalytics', 
            'performanceMetrics',
            'monthlyTrends',
            'retailStore'
        ));
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
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
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
            'opening_time' => $request->opening_time . ':00',
            'closing_time' => $request->closing_time . ':00',
            'status' => 'active',
        ]);

        return redirect()->route('retailer.dashboard')->with('success', 'Retail store profile created successfully!');
    }

    /**
     * Show the form for editing an order
     */
    public function editOrder($id)
    {
        $user = auth()->user();
        $order = \App\Models\Order::where('retailer_id', $user->id)->findOrFail($id);
        return view('retailer.orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function updateOrder(Request $request, $id)
    {
        $user = auth()->user();
        $order = \App\Models\Order::where('retailer_id', $user->id)->findOrFail($id);
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'shipping_country' => 'required|string',
        ]);
        $order->update($request->only([
            'quantity',
            'due_date',
            'notes',
            'shipping_address',
            'shipping_city',
            'shipping_state',
            'shipping_zip',
            'shipping_country',
        ]));
        return redirect()->route('retailer.orders')->with('success', 'Order updated successfully!');
    }

    /**
     * Show form to add a product to inventory
     */
    public function createInventory()
    {
        return view('retailer.inventory.create');
    }

    /**
     * Store a new product in inventory
     */
    public function storeInventory(Request $request)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);
        // Find or create product
        $product = \App\Models\Product::firstOrCreate(
            ['name' => $request->product_name],
            [
                'is_active' => true,
                'sku' => strtoupper('SKU-' . uniqid())
            ]
        );
        // Find or create inventory
        $inventory = \App\Models\Inventory::firstOrNew([
            'retail_store_id' => $retailStore->id,
            'product_id' => $product->id,
        ]);
        $inventory->quantity += $request->quantity;
        $inventory->retail_store_id = $retailStore->id;
        $inventory->location_id = $retailStore->id;
        $inventory->save();
        \Log::info('Saved inventory:', $inventory->toArray());
        \Log::info('Current retail store ID:', ['retail_store_id' => $retailStore->id]);
        return redirect()->route('retailer.inventory')->with('success', 'Product added/updated in inventory!');
    }

    /**
     * Show form to edit an inventory item
     */
    public function editInventory($id)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        $inventory = \App\Models\Inventory::where('retail_store_id', $retailStore->id)->findOrFail($id);
        return view('retailer.inventory.edit', compact('inventory'));
    }

    /**
     * Update an inventory item (retailer)
     */
    public function retailerUpdateInventory(Request $request, $id)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        $inventory = \App\Models\Inventory::where('retail_store_id', $retailStore->id)->findOrFail($id);
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
        ]);
        $inventory->quantity = $request->quantity;
        $inventory->save();
        // Update min_stock_level on the product
        if ($inventory->product) {
            $inventory->product->min_stock_level = $request->min_stock_level;
            $inventory->product->save();
        }
        return redirect()->route('retailer.inventory')->with('success', 'Inventory updated!');
    }

    /**
     * Delete an inventory item
     */
    public function destroyInventory($id)
    {
        $user = auth()->user();
        $retailStore = $user->managedRetailStore;
        $inventory = \App\Models\Inventory::where('retail_store_id', $retailStore->id)->findOrFail($id);
        $inventory->delete();
        return redirect()->route('retailer.inventory')->with('success', 'Inventory item deleted!');
    }
}
