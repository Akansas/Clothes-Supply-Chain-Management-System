<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialSupplier;
use App\Models\Product;
use App\Models\Manufacturer;
use App\Models\User;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SupplierAnalyticsService;

class DashboardController extends Controller
{
    /**
     * Show supplier dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        if (!$supplier) {
            return redirect()->route('supplier.profile.create')->with('error', 'Please complete your supplier profile first.');
        }

        // Statistics
        $stats = [
            'total_materials' => Product::where('supplier_id', $supplier->id)->count(),
            'active_orders' => Order::where('supplier_id', $supplier->id)
                ->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed_deliveries' => Delivery::where('supplier_id', $supplier->id)
                ->where('status', 'delivered')->count(),
            'total_revenue' => Order::where('supplier_id', $supplier->id)
                ->where('status', 'delivered')->sum('total_amount'),
            'monthly_orders' => Order::where('supplier_id', $supplier->id)
                ->whereMonth('created_at', now()->month)->count(),
            'pending_deliveries' => Delivery::where('supplier_id', $supplier->id)
                ->whereIn('status', ['pending', 'in_transit'])->count(),
        ];

        // Recent orders
        $recentOrders = Order::where('supplier_id', $supplier->id)
            ->with(['manufacturer', 'orderItems.product'])
            ->latest()
            ->take(5)
            ->get();

        // Pending deliveries
        $pendingDeliveries = Delivery::where('supplier_id', $supplier->id)
            ->whereIn('status', ['pending', 'in_transit'])
            ->with(['order.manufacturer'])
            ->latest()
            ->take(5)
            ->get();

        // Top materials
        $topMaterials = Product::where('supplier_id', $supplier->id)
            ->withCount(['orderItems as total_ordered' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_ordered', 'desc')
            ->take(5)
            ->get();

        // Fetch orders for this supplier's materials
        $productIds = \App\Models\Product::where('supplier_id', $supplier->id)->pluck('id');
        $orders = \App\Models\Order::whereHas('orderItems', function($q) use ($productIds) {
            $q->whereIn('product_id', $productIds);
        })->with(['orderItems.product', 'user'])->latest()->get();

        return view('supplier.dashboard', compact('stats', 'recentOrders', 'pendingDeliveries', 'topMaterials', 'supplier', 'orders'))->with('user', $user);
    }

    /**
     * Show deliveries
     */
    public function deliveries(Request $request)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $query = Delivery::where('supplier_id', $supplier->id)
            ->with(['order.manufacturer', 'driver']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $deliveries = $query->latest()->paginate(15);

        return view('supplier.deliveries.index', compact('deliveries'));
    }

    /**
     * Show delivery details
     */
    public function showDelivery($id)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $delivery = Delivery::where('supplier_id', $supplier->id)
            ->with(['order.manufacturer', 'order.orderItems.product', 'driver'])
            ->findOrFail($id);

        return view('supplier.deliveries.show', compact('delivery'));
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus(Request $request, $id)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $delivery = Delivery::where('supplier_id', $supplier->id)->findOrFail($id);
        
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
        
        return redirect()->route('supplier.deliveries.show', $delivery->id)
            ->with('success', 'Delivery status updated successfully!');
    }

    /**
     * Show manufacturers
     */
    public function manufacturers()
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $manufacturers = Manufacturer::whereHas('rawMaterialSuppliers', function ($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        })
        ->with(['user'])
        ->paginate(15);

        return view('supplier.manufacturers.index', compact('manufacturers'));
    }

    /**
     * Show analytics
     */
    public function analytics()
    {
        $user = auth()->user();
        $service = new SupplierAnalyticsService($user);

        $demandForecasting = $service->getDemandForecasting();
        $leadTimeTracking = $service->getLeadTimeTracking();
        $materialCostAnalytics = $service->getMaterialCostAnalytics();
        $qualityControlAnalysis = $service->getQualityControlAnalysis();
        $clientSatisfaction = $service->getClientSatisfaction();
        $capacityPlanning = $service->getCapacityPlanning();

        return view('supplier.analytics', compact(
            'demandForecasting',
            'leadTimeTracking',
            'materialCostAnalytics',
            'qualityControlAnalysis',
            'clientSatisfaction',
            'capacityPlanning'
        ));
    }

    /**
     * Show profile
     */
    public function profile()
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        return view('supplier.profile.index', compact('user', 'supplier'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'required|string',
            'supplier_phone' => 'required|string|max:20',
            'specializations' => 'nullable|string',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));
        
        if ($supplier) {
            $supplier->update([
                'name' => $request->supplier_name,
                'address' => $request->supplier_address,
                'phone' => $request->supplier_phone,
                'specializations' => $request->specializations,
            ]);
        }

        return redirect()->route('supplier.profile')->with('success', 'Profile updated successfully!');
    }
}
