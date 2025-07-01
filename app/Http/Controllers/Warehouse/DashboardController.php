<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Delivery;
use App\Models\DeliveryPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show warehouse dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        if (!$warehouse) {
            return redirect()->route('warehouse.profile.create')->with('error', 'Please complete your warehouse profile first.');
        }

        // Statistics
        $stats = [
            'total_products' => Inventory::where('warehouse_id', $warehouse->id)->count(),
            'low_stock_products' => Inventory::where('warehouse_id', $warehouse->id)->where('quantity', '<', 20)->count(),
            'out_of_stock' => Inventory::where('warehouse_id', $warehouse->id)->where('quantity', 0)->count(),
            'pending_fulfillments' => Order::where('status', 'confirmed')->where('source', 'retailer')->count(),
            'total_value' => Inventory::where('warehouse_id', $warehouse->id)
                ->join('products', 'inventories.product_id', '=', 'products.id')
                ->selectRaw('SUM(inventories.quantity * products.price) as total')
                ->first()->total ?? 0,
            'monthly_fulfillments' => Order::where('status', 'shipped')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ];

        // Recent inventory movements
        $recentMovements = Inventory::where('warehouse_id', $warehouse->id)
            ->with('product')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // Pending fulfillments
        $pendingFulfillments = Order::where('status', 'confirmed')
            ->where('source', 'retailer')
            ->with(['user', 'orderItems.product'])
            ->latest()
            ->take(5)
            ->get();

        // Low stock alerts
        $lowStockItems = Inventory::where('warehouse_id', $warehouse->id)
            ->where('quantity', '<', 20)
            ->with('product')
            ->get();

        return view('warehouse.dashboard', compact('stats', 'recentMovements', 'pendingFulfillments', 'lowStockItems', 'warehouse'));
    }

    /**
     * Show inventory management
     */
    public function inventory(Request $request)
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        $query = Inventory::where('warehouse_id', $warehouse->id)
            ->with('product');

        // Filter by stock level
        if ($request->stock_level) {
            switch ($request->stock_level) {
                case 'low':
                    $query->where('quantity', '<', 20);
                    break;
                case 'out':
                    $query->where('quantity', 0);
                    break;
                case 'available':
                    $query->where('quantity', '>', 0);
                    break;
            }
        }

        // Filter by product name
        if ($request->search) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $inventory = $query->paginate(15);

        return view('warehouse.inventory.index', compact('inventory'));
    }

    /**
     * Update inventory quantity
     */
    public function updateInventory(Request $request, $id)
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        $inventory = Inventory::where('warehouse_id', $warehouse->id)->findOrFail($id);
        
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $inventory->update([
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
            'notes' => $request->notes,
        ]);

        return redirect()->route('warehouse.inventory')->with('success', 'Inventory updated successfully!');
    }

    /**
     * Add new product to inventory
     */
    public function addToInventory(Request $request)
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'location' => 'nullable|string',
        ]);

        // Check if product already exists in inventory
        $existing = Inventory::where('warehouse_id', $warehouse->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return redirect()->route('warehouse.inventory')->with('error', 'Product already exists in inventory!');
        }

        Inventory::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
            'location' => $request->location,
        ]);

        return redirect()->route('warehouse.inventory')->with('success', 'Product added to inventory successfully!');
    }

    /**
     * Show order fulfillments
     */
    public function fulfillments(Request $request)
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        $query = Order::where('status', 'confirmed')
            ->where('source', 'retailer')
            ->with(['user', 'orderItems.product']);

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $fulfillments = $query->latest()->paginate(15);

        return view('warehouse.fulfillments.index', compact('fulfillments'));
    }

    /**
     * Show fulfillment details
     */
    public function showFulfillment($id)
    {
        $order = Order::where('status', 'confirmed')
            ->where('source', 'retailer')
            ->with(['user', 'orderItems.product'])
            ->findOrFail($id);

        return view('warehouse.fulfillments.show', compact('order'));
    }

    /**
     * Process fulfillment
     */
    public function processFulfillment(Request $request, $id)
    {
        $order = Order::where('status', 'confirmed')
            ->where('source', 'retailer')
            ->findOrFail($id);
        
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        $request->validate([
            'action' => 'required|in:fulfill,partial,insufficient',
            'notes' => 'nullable|string',
        ]);

        switch ($request->action) {
            case 'fulfill':
                // Check if we have sufficient inventory
                $insufficientItems = [];
                foreach ($order->orderItems as $item) {
                    $inventory = Inventory::where('warehouse_id', $warehouse->id)
                        ->where('product_id', $item->product_id)
                        ->first();
                    
                    if (!$inventory || $inventory->quantity < $item->quantity) {
                        $insufficientItems[] = [
                            'product' => $item->product->name,
                            'required' => $item->quantity,
                            'available' => $inventory ? $inventory->quantity : 0,
                        ];
                    }
                }

                if (!empty($insufficientItems)) {
                    return back()->with('error', 'Insufficient inventory for some items.')->with('insufficient_items', $insufficientItems);
                }

                // Deduct inventory and update order
                foreach ($order->orderItems as $item) {
                    $inventory = Inventory::where('warehouse_id', $warehouse->id)
                        ->where('product_id', $item->product_id)
                        ->first();
                    
                    $inventory->decrement('quantity', $item->quantity);
                }

                $order->update([
                    'status' => 'processing',
                    'notes' => 'Fulfilled by warehouse: ' . $request->notes,
                ]);

                // Create delivery
                $this->createDelivery($order);

                return redirect()->route('warehouse.fulfillments')->with('success', 'Order fulfilled successfully!');

            case 'partial':
                // Handle partial fulfillment
                $order->update([
                    'status' => 'processing',
                    'notes' => 'Partially fulfilled: ' . $request->notes,
                ]);
                return redirect()->route('warehouse.fulfillments')->with('success', 'Partial fulfillment processed!');

            case 'insufficient':
                // Mark as insufficient inventory
                $order->update([
                    'status' => 'pending',
                    'notes' => 'Insufficient inventory: ' . $request->notes,
                ]);
                return redirect()->route('warehouse.fulfillments')->with('warning', 'Order marked as insufficient inventory!');
        }
    }

    /**
     * Create delivery for order
     */
    private function createDelivery($order)
    {
        // Find available delivery partner
        $deliveryPartner = DeliveryPartner::where('status', 'active')->first();
        
        if ($deliveryPartner) {
            Delivery::create([
                'order_id' => $order->id,
                'driver_id' => $deliveryPartner->user_id,
                'status' => 'pending',
                'tracking_number' => 'TRK-' . time(),
                'estimated_delivery' => now()->addDays(3),
                'pickup_location' => 'Warehouse',
                'delivery_address' => $order->shipping_address,
            ]);
        }
    }

    /**
     * Show deliveries
     */
    public function deliveries(Request $request)
    {
        $query = Delivery::with(['order.user', 'driver']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $deliveries = $query->latest()->paginate(15);

        return view('warehouse.deliveries.index', compact('deliveries'));
    }

    /**
     * Show delivery details
     */
    public function showDelivery($id)
    {
        $delivery = Delivery::with(['order.user', 'order.orderItems.product', 'driver'])
            ->findOrFail($id);

        return view('warehouse.deliveries.show', compact('delivery'));
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,in_transit,out_for_delivery,delivered',
            'notes' => 'nullable|string',
        ]);
        
        $delivery->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        
        // If delivered, update order status
        if ($request->status === 'delivered') {
            $delivery->order->update(['status' => 'delivered']);
        }
        
        return redirect()->route('warehouse.deliveries.show', $delivery->id)->with('success', 'Delivery status updated successfully!');
    }

    /**
     * Show analytics/reports
     */
    public function analytics()
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        // Monthly fulfillment data
        $monthlyFulfillments = Order::where('status', 'shipped')
            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
            ->whereYear('updated_at', now()->year)
            ->groupBy('month')
            ->get();

        // Top products by quantity
        $topProducts = Inventory::where('warehouse_id', $warehouse->id)
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('products.name, inventories.quantity, (inventories.quantity * products.price) as total_value')
            ->orderBy('quantity', 'desc')
            ->take(10)
            ->get();

        // Inventory value by category
        $inventoryByCategory = Inventory::where('warehouse_id', $warehouse->id)
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('products.category, SUM(inventories.quantity) as total_quantity, SUM(inventories.quantity * products.price) as total_value')
            ->groupBy('products.category')
            ->get();

        return view('warehouse.analytics', compact('monthlyFulfillments', 'topProducts', 'inventoryByCategory'));
    }

    /**
     * Show profile management
     */
    public function profile()
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        return view('warehouse.profile.index', compact('user', 'warehouse'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'warehouse_name' => 'required|string|max:255',
            'warehouse_address' => 'required|string',
            'warehouse_phone' => 'required|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));
        
        if ($warehouse) {
            $warehouse->update([
                'name' => $request->warehouse_name,
                'address' => $request->warehouse_address,
                'phone' => $request->warehouse_phone,
            ]);
        }

        return redirect()->route('warehouse.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show form to assign delivery to delivery personnel.
     */
    public function assignDelivery()
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        $orders = \App\Models\Order::where('warehouse_id', $warehouse->id)->where('status', 'confirmed')->get();
        $deliveryPartners = \App\Models\DeliveryPartner::where('status', 'active')->get();
        return view('warehouse.assign-delivery', compact('orders', 'deliveryPartners'));
    }

    /**
     * Show chat interface for warehouse manager.
     */
    public function chat()
    {
        $user = auth()->user();
        $warehouse = $user->managedWarehouse;
        // Fetch conversations with vendors, retailers, delivery personnel
        $conversations = \App\Models\Conversation::where('warehouse_id', $warehouse->id)->latest()->get();
        return view('warehouse.chat', compact('conversations'));
    }
}
