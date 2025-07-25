<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use App\Models\ProductionOrder;
use App\Models\ProductionStage;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Manufacturer;
use App\Models\User;
use App\Models\Role;
use App\Models\RawMaterialSupplier;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Worker;
use App\Models\SupplyCenter;
use App\Models\Shift;
use App\Models\Customer;

class DashboardController extends Controller
{
    /**
     * Show manufacturer dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $manufacturer = \App\Models\Manufacturer::first();
        // Statistics
        $stats = [
            'total_production_orders' => ProductionOrder::where('manufacturer_id', $user->id)->count(),
            'active_production_orders' => ProductionOrder::where('manufacturer_id', $user->id)
                ->whereIn('status', ['in_progress', 'pending'])->count(),
            'completed_orders' => ProductionOrder::where('manufacturer_id', $user->id)
                ->where('status', 'completed')->count(),
            'total_products' => Product::where('manufacturer_id', $manufacturer ? $manufacturer->id : null)->count(),
            'monthly_production' => ProductionOrder::where('manufacturer_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->sum('quantity'),
            // Workforce stats
            'total_workers' => Worker::where('manufacturer_id', $user->manufacturer_id)->count(),
            'total_supply_centers' => SupplyCenter::where('manufacturer_id', $user->manufacturer_id)->count(),
            'total_shifts' => Shift::where('manufacturer_id', $user->manufacturer_id)->count(),
        ];
        // Workforce charts
        $supplyCenters = SupplyCenter::where('manufacturer_id', $user->manufacturer_id)->get();
        $workersBySupplyCenter = [
            'labels' => $supplyCenters->pluck('name')->toArray(),
            'data' => $supplyCenters->map(function($center) {
                return $center->workers()->count();
            })->toArray(),
        ];
        $shifts = Shift::where('manufacturer_id', $user->manufacturer_id)->get();
        $workersByShift = [
            'labels' => $shifts->pluck('name')->toArray(),
            'data' => $shifts->map(function($shift) {
                return $shift->assignments()->count();
            })->toArray(),
        ];
        $charts = [
            'workers_by_supply_center' => $workersBySupplyCenter,
            'workers_by_shift' => $workersByShift,
        ];
        // Recent production orders
        $recentOrders = ProductionOrder::where('manufacturer_id', $user->id)
            ->with(['product', 'stages'])
            ->latest()
            ->take(5)
            ->get();
        // Finished products for the table (use shared manufacturer profile)
        $finishedProducts = Product::where('manufacturer_id', $manufacturer ? $manufacturer->id : null)
            ->whereNull('supplier_id')
            ->with('inventory')
            ->get();
        // Raw materials for the table
        $rawMaterials = Product::whereNotNull('supplier_id')
            ->with(['inventory', 'supplier.user'])
            ->get();
        // Debug: Log the products being fetched
        Log::info('Manufacturer Dashboard Products', [
            'manufacturer_id' => $manufacturer ? $manufacturer->id : null,
            'finished_products' => $finishedProducts->toArray(),
            'raw_materials' => $rawMaterials->toArray(),
        ]);
        // Production stages in progress
        $activeStages = ProductionStage::whereHas('productionOrder', function ($query) use ($user) {
            $query->where('manufacturer_id', $user->id);
        })
        ->where('status', 'in_progress')
        ->with(['productionOrder.product'])
        ->latest()
        ->take(5)
        ->get();
        // Fetch conversations where the manufacturer is a participant
        //$conversations = $user->conversations()
          //  ->with(['messages.user', 'participants'])
           // ->latest('updated_at')
           // ->get();
        // Remove all code related to retailerOrders and retailer production orders management.

        // Remove ML integration and customer segmentation/forecasting

        // Fetch recent purchase orders for the manufacturer
        $recentPurchaseOrders = \App\Models\Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->with(['supplier.user'])
            ->get();

        // --- NEW DASHBOARD CARDS DATA ---
        // Use the same manufacturer ID logic as the retailer orders table
        $manufacturerId = auth()->id();
        // Purchase Orders (from suppliers)
        $purchaseOrderStatuses = ['pending', 'approved', 'rejected', 'delivered', 'cancelled'];
        $purchaseOrdersStats = [];
        foreach ($purchaseOrderStatuses as $status) {
            $purchaseOrdersStats[$status] = \App\Models\Order::where('user_id', $user->id)->where('status', $status)->count();
        }
        // Retailer Orders (to retailers)
        $retailerOrderStatuses = ['pending', 'approved', 'rejected', 'delivered', 'cancelled'];
        $retailerOrdersStats = [];
        foreach ($retailerOrderStatuses as $status) {
            $retailerOrdersStats[$status] = \App\Models\ProductionOrder::where('manufacturer_id', $manufacturerId)->whereNotNull('retailer_id')->where('status', $status)->count();
        }
        // Total Cost (sum of total_amount for delivered purchase orders)
        $totalCost = \App\Models\Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->sum('total_amount');
        
        $totalRevenue = \App\Models\ProductionOrder::where('manufacturer_id', $manufacturerId)
            ->whereNotNull('retailer_id')
            ->where('status', 'delivered')
            ->with('product')
            ->get()
            ->sum(function($order) {
                return ($order->product && $order->quantity) ? $order->product->price * $order->quantity : 0;
            });

            $supplierRole = Role::where('name', 'raw_material_supplier')->first();
        $suppliers = $supplierRole ? User::where('role_id', $supplierRole->id)->get() : collect();



        // Get all retailer users
        $retailerRole = Role::where('name', 'retailer')->first();
        $retailers = $retailerRole ? User::where('role_id', $retailerRole->id)->get() : collect();



        return view('manufacturer.dashboard', compact(
            'stats',
            'recentOrders',
            'activeStages',
            'user',
            //'conversations',
            'finishedProducts',
            'rawMaterials',
            'recentPurchaseOrders',
            // Add new variables for cards
            'purchaseOrdersStats',
            'retailerOrdersStats',
            'totalCost',
            'totalRevenue',
            'suppliers',
            'retailers'
        ));
    }

    /**
     * Show production orders
     */
    public function productionOrders(Request $request)
    {
        $user = auth()->user();
        $manufacturer = $user->manufacturer;
        
        $query = ProductionOrder::where('manufacturer_id', $manufacturer->id)
            ->with(['product', 'stages']);

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

        return view('manufacturer.production-orders.index', compact('orders'));
    }

    /**
     * Show production order details
     */
    public function showProductionOrder($id)
    {
        $manufacturer = \App\Models\Manufacturer::first();
        $order = ProductionOrder::where('manufacturer_id', $manufacturer ? $manufacturer->id : null)
            ->with(['product', 'stages', 'retailer'])
            ->findOrFail($id);
        return view('manufacturer.production-orders.show', compact('order'));
    }

    /**
     * Create new production order
     */
    public function createProductionOrder()
    {
        $user = auth()->user();
        $manufacturer = $user->manufacturer;

        if (!$manufacturer) {
            return redirect()->back()->with('error', 'Manufacturer profile not found. Please complete your manufacturer profile.');
        }

        $products = Product::where('manufacturer_id', $manufacturer->id)->get();

        return view('manufacturer.production-orders.create', compact('products'));
    }

    /**
     * Store new production order
     */
    public function storeProductionOrder(Request $request)
    {
        $user = auth()->user();
        $manufacturer = $user->manufacturer;

        if (!$manufacturer) {
            return redirect()->back()->with('error', 'Manufacturer profile not found. Please complete your manufacturer profile.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after:today',
            'expected_completion' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
        ]);

        $order = ProductionOrder::create([
            'manufacturer_id' => $manufacturer->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'start_date' => $request->start_date,
            'expected_completion' => $request->expected_completion,
            'priority' => $request->priority,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Create default production stages
        $this->createDefaultStages($order);

        return redirect()->route('manufacturer.production-orders.show', $order->id)
            ->with('success', 'Production order created successfully!');
    }

    /**
     * Browse raw materials from suppliers.
     */
    public function browseMaterials()
    {
        $materials = Product::whereNotNull('supplier_id')->with('supplier')->latest()->paginate(20);

        return view('manufacturer.materials.browse', compact('materials'));
    }

    /**
     * Create default production stages
     */
    private function createDefaultStages($order)
    {
        $stages = [
            ['name' => 'Material Preparation', 'description' => 'Prepare raw materials', 'duration' => 2],
            ['name' => 'Assembly', 'description' => 'Assemble components', 'duration' => 3],
            ['name' => 'Quality Testing', 'description' => 'Test product quality', 'duration' => 1],
            ['name' => 'Packaging', 'description' => 'Package finished products', 'duration' => 1],
        ];

        foreach ($stages as $stage) {
            ProductionStage::create([
                'production_order_id' => $order->id,
                'name' => $stage['name'],
                'description' => $stage['description'],
                'duration' => $stage['duration'],
                'status' => 'pending',
                'start_date' => null,
                'completion_date' => null,
            ]);
        }
    }

    /**
     * Update production order status
     */
    public function updateProductionOrderStatus(Request $request, $id)
    {
        $user = auth()->user();
        $order = ProductionOrder::where('manufacturer_id', $user->id)->findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,delivered',
        ]);
        $order->status = $request->status;
        $order->save();
        // Redirect to the production orders list for manufacturer (best UX)
        return redirect()->route('manufacturer.production-orders')->with('success', 'Order status updated.');
    }

    /**
     * Add completed products to inventory
     */
    private function addToInventory($order)
    {
        // Find or create inventory record
        $inventory = Inventory::where('manufacturer_id', $order->manufacturer_id)
            ->where('product_id', $order->product_id)
            ->first();

        if ($inventory) {
            $inventory->increment('quantity', $order->quantity);
        } else {
            Inventory::create([
                'manufacturer_id' => $order->manufacturer_id,
                'product_id' => $order->product_id,
                'quantity' => $order->quantity,
                'reorder_level' => 10,
            ]);
        }
    }

    /**
     * Helper to get manufacturer or redirect with error.
     */
    private function getManufacturerOrRedirect()
    {
        $user = auth()->user();
        $manufacturer = $user->manufacturer;
        if (!$manufacturer) {
            // Redirect back with error
            redirect()->back()->with('error', 'Manufacturer profile not found. Please complete your manufacturer profile.')->send();
            exit; // Ensure no further code runs
        }
        return $manufacturer;
    }

    /**
     * Show production stages
     */
    public function productionStages(Request $request)
    {
        $manufacturer = $this->getManufacturerOrRedirect();
        $query = ProductionStage::whereHas('productionOrder', function ($q) use ($manufacturer) {
            $q->where('manufacturer_id', $manufacturer->id);
        })
        ->with(['productionOrder.product']);
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $stages = $query->latest()->paginate(15);
        return view('manufacturer.production-stages.index', compact('stages'));
    }

    /**
     * Update production stage status
     */
    public function updateStageStatus(Request $request, $id)
    {
        $manufacturer = $this->getManufacturerOrRedirect();
        $stage = ProductionStage::whereHas('productionOrder', function ($q) use ($manufacturer) {
            $q->where('manufacturer_id', $manufacturer->id);
        })->findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
        ]);
        $stage->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        if ($request->status === 'in_progress' && !$stage->start_date) {
            $stage->start_date = now();
            $stage->save();
        } elseif ($request->status === 'completed') {
            $stage->completion_date = now();
            $stage->save();
        }
        return redirect()->back()->with('success', 'Stage status updated successfully!');
    }

    /**
     * Show suppliers
     */
    public function suppliers()
    {
        $manufacturer = $this->getManufacturerOrRedirect();
        $suppliers = RawMaterialSupplier::where('manufacturer_id', $manufacturer->id)
            ->with('supplier')
            ->paginate(15);
        return view('manufacturer.suppliers.index', compact('suppliers'));
    }

    /**
     * Show inventory
     */
    public function inventory(Request $request)
    {
        $manufacturer = $this->getManufacturerOrRedirect();
        $query = Inventory::where('manufacturer_id', $manufacturer->id)
            ->with('product');
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
        $inventory = $query->paginate(15);
        return view('manufacturer.inventory.index', compact('inventory'));
    }

    /**
     * Show profile
     */
    public function showProfile()
    {
        $manufacturer = auth()->user()->manufacturer;
        return view('manufacturer.profile.show', compact('manufacturer'));
    }

    /**
     * Edit profile
     */
    public function editProfile()
    {
        $manufacturer = auth()->user()->manufacturer;
        return view('manufacturer.profile.edit', compact('manufacturer'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $manufacturer = auth()->user()->manufacturer;
        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
        ]);
        $manufacturer->update($request->only(['company_name', 'address', 'phone', 'email']));
        return redirect()->route('manufacturer.profile')->with('success', 'Profile updated successfully!');
    }

    public function orderMaterial($materialId)
    {
        $material = \App\Models\Product::with('supplier.user')->where('supplier_id', '!=', null)->findOrFail($materialId);
        return view('manufacturer.materials.order', compact('material'));
    }

    public function placeMaterialOrder(Request $request, $materialId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $user = auth()->user();
        $material = \App\Models\Product::with('supplier')->where('supplier_id', '!=', null)->findOrFail($materialId);

        // Debug log before creating the order
        Log::info('DEBUG: Material for order', [
            'material_id' => $material->id,
            'material_supplier_id' => $material->supplier_id,
            'material' => $material->toArray(),
        ]);

        // Create the order
        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'supplier_id' => $material->supplier_id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'status' => 'pending',
            'total_amount' => $material->price * $request->quantity,
            'notes' => 'Raw material order',
            'shipping_address' => $user->company_name ?? 'To be provided',
            'shipping_city' => 'To be provided',
            'shipping_state' => 'To be provided',
            'shipping_zip' => 'To be provided',
            'shipping_country' => 'To be provided',
            'billing_address' => 'To be provided',
        ]);

        // Debug log for order creation
        Log::info('Raw material order placed', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'supplier_id' => $material->supplier_id,
        ]);

        // Create the order item
        $order->orderItems()->create([
            'product_id' => $material->id,
            'quantity' => $request->quantity,
            'unit_price' => $material->price,
            'total_price' => $material->price * $request->quantity,
        ]);

        return redirect()->route('manufacturer.materials.browse')->with('success', 'Order placed successfully!');
    }

    /**
     * Start a production order
     */
    public function startProductionOrder($id)
    {
        $user = auth()->user();
        $order = ProductionOrder::where('manufacturer_id', $user->id)->findOrFail($id);
        if (in_array($order->status, ['approved', 'paused'])) {
            $order->startProduction();
        }
        return redirect()->back()->with('success', 'Production started.');
    }

    /**
     * Pause a production order
     */
    public function pauseProductionOrder($id)
    {
        $user = auth()->user();
        $order = ProductionOrder::where('manufacturer_id', $user->id)->findOrFail($id);
        if ($order->status === 'in_production') {
            $order->status = 'paused';
            $order->save();
        }
        return redirect()->back()->with('success', 'Production paused.');
    }

    /**
     * Complete a production order
     */
    public function completeProductionOrder(Request $request, $id)
    {
        $user = auth()->user();
        $order = ProductionOrder::where('manufacturer_id', $user->id)->findOrFail($id);
        if ($order->status === 'in_production') {
            $order->produced_quantity = $request->produced_quantity ?? $order->quantity;
            $order->completeProduction();
            $this->addToInventory($order);
        }
        return redirect()->back()->with('success', 'Production completed.');
    }

    /**
     * Mark order as ready to ship and add shipment info
     */
    public function markReadyToShip(Request $request, $id)
    {
        $user = auth()->user();
        $order = ProductionOrder::where('manufacturer_id', $user->id)->findOrFail($id);
        if ($order->status === 'completed') {
            $order->status = 'ready_to_ship';
            $order->shipment_date = $request->shipment_date ?? now();
            $order->tracking_number = $request->tracking_number;
            $order->save();
        }
        return redirect()->back()->with('success', 'Order marked as ready to ship.');
    }

    public function deliverProductionOrder(Request $request, $id)
    {
        $user = auth()->user();
        $order = ProductionOrder::where('manufacturer_id', $user->id)->findOrFail($id);
        if ($order->status === 'approved') {
            $order->status = 'delivered';
            $order->save();
        }
        return redirect()->route('manufacturer.dashboard')->with('success', 'Order marked as delivered.');
    }

    /**
     * Start a production stage
     */
    public function startProductionStage($id)
    {
        $stage = ProductionStage::findOrFail($id);
        if ($stage->status === 'pending') {
            $stage->startStage();
        }
        return redirect()->back()->with('success', 'Stage started.');
    }

    /**
     * Pause a production stage
     */
    public function pauseProductionStage($id)
    {
        $stage = ProductionStage::findOrFail($id);
        if ($stage->status === 'in_progress') {
            $stage->status = 'paused';
            $stage->save();
        }
        return redirect()->back()->with('success', 'Stage paused.');
    }

    /**
     * Complete a production stage
     */
    public function completeProductionStage(Request $request, $id)
    {
        $stage = ProductionStage::findOrFail($id);
        if ($stage->status === 'in_progress') {
            $stage->actual_quantity = $request->actual_quantity ?? null;
            $stage->completeStage();
        }
        return redirect()->back()->with('success', 'Stage completed.');
    }

    /**
     * List purchase orders placed by the manufacturer to suppliers.
     */
    public function purchaseOrders()
    {
        $user = auth()->user();
        $orders = \App\Models\Order::where('user_id', $user->id)->latest()->paginate(15);
        return view('manufacturer.purchase-orders.index', compact('orders'));
    }

    /**
     * Show the edit form for a purchase order.
     */
    public function editPurchaseOrder($orderId)
    {
        $user = auth()->user();
        $order = \App\Models\Order::where('user_id', $user->id)->findOrFail($orderId);
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return redirect()->route('manufacturer.purchase-orders')->with('error', 'Only pending or confirmed orders can be edited.');
        }
        $order->load(['orderItems.product']);
        return view('manufacturer.purchase-orders.edit', compact('order'));
    }

    /**
     * Update a purchase order.
     */
    public function updatePurchaseOrder(Request $request, $orderId)
    {
        $user = auth()->user();
        $order = \App\Models\Order::where('user_id', $user->id)->findOrFail($orderId);
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return redirect()->route('manufacturer.purchase-orders')->with('error', 'Only pending or confirmed orders can be updated.');
        }
        $request->validate([
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'shipping_country' => 'required|string',
            'billing_address' => 'required|string',
            'order_items' => 'array',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);
        $order->update($request->only(['shipping_address', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country', 'billing_address']));
        $total = 0;
        foreach ($request->order_items as $itemId => $itemData) {
            $item = $order->orderItems()->find($itemId);
            if ($item) {
                $item->quantity = $itemData['quantity'];
                $item->total_price = $item->unit_price * $item->quantity;
                $item->save();
                $total += $item->total_price;
            }
        }
        $order->total_amount = $total;
        $order->save();
        return redirect()->route('manufacturer.purchase-orders')->with('success', 'Order updated successfully.');
    }

    /**
     * Cancel a purchase order.
     */
    public function cancelPurchaseOrder($orderId)
    {
        $user = auth()->user();
        $order = \App\Models\Order::where('user_id', $user->id)->findOrFail($orderId);
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return redirect()->route('manufacturer.purchase-orders')->with('error', 'Only pending or confirmed orders can be cancelled.');
        }
        $order->status = 'cancelled';
        $order->save();
        return redirect()->route('manufacturer.purchase-orders')->with('success', 'Order cancelled successfully.');
    }

    public function markAsDelivered(Request $request, $id)
    {
        $user = auth()->user();
        $manufacturer = $user->manufacturer;
        $order = ProductionOrder::where('manufacturer_id', $manufacturer->id)->findOrFail($id);
        if (in_array($order->status, ['ready_to_ship', 'shipped'])) {
            $order->status = 'delivered';
            $order->delivered_at = now();
            $order->save();
        }
        return redirect()->back()->with('success', 'Order marked as delivered.');
    }

    /**
     * Show the order processing page
     */
    public function orderProcessing()
    {
        return view('manufacturer.order-processing');
    }

    /**
     * Show details for a single purchase order.
     */
    public function showPurchaseOrder($orderId)
    {
        $user = auth()->user();
        $order = \App\Models\Order::where('user_id', $user->id)
            ->with(['supplier.user', 'orderItems.product'])
            ->findOrFail($orderId);
        return view('manufacturer.purchase-orders.show', compact('order'));
    }

    public function retailerOrders()
    {
        $manufacturerId = auth()->id();
        $orders = \App\Models\ProductionOrder::where('manufacturer_id', $manufacturerId)
            ->whereNotNull('retailer_id')
            ->with(['product', 'retailer'])
            ->latest()
            ->paginate(20);
        return view('manufacturer.retailer-orders', compact('orders'));
    }

    public function updateRetailerOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,delivered',
        ]);
        $productionOrder = \App\Models\ProductionOrder::findOrFail($id);
        $productionOrder->status = $request->status;
        $productionOrder->save();
        // Also update the corresponding retailer order (by order_number)
        $order = \App\Models\Order::where('order_number', $productionOrder->order_number)->first();
        if ($order) {
            $order->status = $request->status;
            $order->save();
        }
        return redirect()->route('manufacturer.retailer-orders')->with('success', 'Order status updated.');
    }
}
