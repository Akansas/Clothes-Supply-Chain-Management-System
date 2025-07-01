<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use App\Models\DeliveryPartner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Delivery::with(['order.customer', 'driver', 'deliveryPartner']);

        // Filter based on user role
        switch ($user->role->name) {
            case 'delivery_personnel':
                $query->where('driver_id', $user->id);
                break;
            case 'warehouse_manager':
                // Get deliveries for orders from this warehouse
                $warehouseId = $user->managedWarehouse->id ?? 0;
                $query->whereHas('order', function ($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId);
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

        if ($request->filled('tracking_number')) {
            $query->where('tracking_number', 'like', '%' . $request->tracking_number . '%');
        }

        $deliveries = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new delivery
     */
    public function create()
    {
        $orders = Order::where('status', 'confirmed')
            ->whereDoesntHave('delivery')
            ->with('customer')
            ->get();
        
        $drivers = User::whereHas('role', function ($q) {
            $q->where('name', 'delivery_personnel');
        })->get();
        
        $deliveryPartners = DeliveryPartner::where('status', 'active')->get();

        return view('deliveries.create', compact('orders', 'drivers', 'deliveryPartners'));
    }

    /**
     * Store a newly created delivery
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'nullable|exists:users,id',
            'delivery_partner_id' => 'nullable|exists:delivery_partners,id',
            'delivery_fee' => 'required|numeric|min:0',
            'estimated_delivery_date' => 'required|date|after:today',
            'delivery_notes' => 'nullable|string'
        ]);

        // Check if order already has a delivery
        if (Delivery::where('order_id', $request->order_id)->exists()) {
            return back()->with('error', 'Order already has a delivery assigned.');
        }

        $delivery = Delivery::create([
            'order_id' => $request->order_id,
            'driver_id' => $request->driver_id,
            'delivery_partner_id' => $request->delivery_partner_id,
            'tracking_number' => 'DEL-' . date('Ymd') . '-' . strtoupper(uniqid()),
            'status' => 'pending',
            'delivery_fee' => $request->delivery_fee,
            'estimated_delivery_date' => $request->estimated_delivery_date,
            'delivery_notes' => $request->delivery_notes,
        ]);

        // Update order status to shipped
        $order = Order::find($request->order_id);
        $order->update(['status' => 'shipped', 'shipped_at' => now()]);

        return redirect()->route('deliveries.show', $delivery->id)
            ->with('success', 'Delivery created successfully!');
    }

    /**
     * Display the specified delivery
     */
    public function show($id)
    {
        $delivery = Delivery::with(['order.customer', 'order.orderItems.product', 'driver', 'deliveryPartner'])
            ->findOrFail($id);

        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified delivery
     */
    public function edit($id)
    {
        $delivery = Delivery::with('order')->findOrFail($id);
        
        $drivers = User::whereHas('role', function ($q) {
            $q->where('name', 'delivery_personnel');
        })->get();
        
        $deliveryPartners = DeliveryPartner::where('status', 'active')->get();

        return view('deliveries.edit', compact('delivery', 'drivers', 'deliveryPartners'));
    }

    /**
     * Update the specified delivery
     */
    public function update(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);

        $request->validate([
            'driver_id' => 'nullable|exists:users,id',
            'delivery_partner_id' => 'nullable|exists:delivery_partners,id',
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,failed',
            'delivery_fee' => 'required|numeric|min:0',
            'estimated_delivery_date' => 'required|date',
            'delivery_notes' => 'nullable|string'
        ]);

        $oldStatus = $delivery->status;
        $delivery->update($request->all());

        // Update timestamps based on status change
        if ($request->status !== $oldStatus) {
            switch ($request->status) {
                case 'assigned':
                    $delivery->update(['assigned_at' => now()]);
                    break;
                case 'picked_up':
                    $delivery->update(['picked_up_at' => now()]);
                    break;
                case 'delivered':
                    $delivery->update(['delivered_at' => now()]);
                    // Update order status
                    $delivery->order->update(['status' => 'delivered', 'delivered_at' => now()]);
                    break;
                case 'failed':
                    // Update order status
                    $delivery->order->update(['status' => 'failed']);
                    break;
            }
        }

        return redirect()->route('deliveries.show', $delivery->id)
            ->with('success', 'Delivery updated successfully!');
    }

    /**
     * Update delivery status
     */
    public function updateStatus(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,failed'
        ]);

        $oldStatus = $delivery->status;
        $delivery->update(['status' => $request->status]);

        // Update timestamps based on status change
        switch ($request->status) {
            case 'assigned':
                $delivery->update(['assigned_at' => now()]);
                break;
            case 'picked_up':
                $delivery->update(['picked_up_at' => now()]);
                break;
            case 'delivered':
                $delivery->update(['delivered_at' => now()]);
                // Update order status
                $delivery->order->update(['status' => 'delivered', 'delivered_at' => now()]);
                break;
            case 'failed':
                // Update order status
                $delivery->order->update(['status' => 'failed']);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Delivery status updated successfully',
            'new_status' => $request->status
        ]);
    }

    /**
     * Track delivery
     */
    public function track($trackingNumber)
    {
        $delivery = Delivery::with(['order.customer', 'driver'])
            ->where('tracking_number', $trackingNumber)
            ->firstOrFail();

        return view('deliveries.track', compact('delivery'));
    }

    /**
     * Get delivery analytics
     */
    public function analytics()
    {
        $user = Auth::user();
        $query = Delivery::query();

        // Filter based on user role
        switch ($user->role->name) {
            case 'delivery_personnel':
                $query->where('driver_id', $user->id);
                break;
        }

        // Analytics data
        $totalDeliveries = $query->count();
        $deliveriesByStatus = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $onTimeDeliveries = $query->where('status', 'delivered')
            ->where('delivered_at', '<=', DB::raw('estimated_delivery_date'))
            ->count();

        $lateDeliveries = $query->where('status', 'delivered')
            ->where('delivered_at', '>', DB::raw('estimated_delivery_date'))
            ->count();

        $deliverySuccessRate = $totalDeliveries > 0 ? 
            (($onTimeDeliveries + $lateDeliveries) / $totalDeliveries) * 100 : 0;

        // Monthly deliveries
        $monthlyDeliveries = $query->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('deliveries.analytics', compact(
            'totalDeliveries',
            'deliveriesByStatus',
            'onTimeDeliveries',
            'lateDeliveries',
            'deliverySuccessRate',
            'monthlyDeliveries'
        ));
    }

    /**
     * Optimize delivery routes
     */
    public function optimizeRoutes()
    {
        $user = Auth::user();
        $pendingDeliveries = Delivery::where('status', 'pending')
            ->with(['order.customer'])
            ->get();

        // Simple route optimization (nearest neighbor algorithm)
        $optimizedRoutes = $this->optimizeDeliveryRoutes($pendingDeliveries);

        return view('deliveries.optimize', compact('optimizedRoutes'));
    }

    /**
     * Assign delivery to driver
     */
    public function assignDriver(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);
        
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        $delivery->update([
            'driver_id' => $request->driver_id,
            'status' => 'assigned',
            'assigned_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Driver assigned successfully'
        ]);
    }

    /**
     * Get proof of delivery
     */
    public function proofOfDelivery($id)
    {
        $delivery = Delivery::with(['order.customer', 'driver'])
            ->where('status', 'delivered')
            ->findOrFail($id);

        return view('deliveries.proof', compact('delivery'));
    }

    /**
     * Simple route optimization algorithm
     */
    private function optimizeDeliveryRoutes($deliveries)
    {
        // This is a simplified version - in a real application, you'd use
        // more sophisticated algorithms and external mapping APIs
        
        $routes = [];
        $currentLocation = [0, 0]; // Starting point (warehouse)
        
        foreach ($deliveries as $delivery) {
            // Calculate distance (simplified)
            $customerLocation = $this->getCustomerLocation($delivery->order->customer);
            $distance = $this->calculateDistance($currentLocation, $customerLocation);
            
            $routes[] = [
                'delivery' => $delivery,
                'distance' => $distance,
                'estimated_time' => $distance * 2 // 2 minutes per unit distance
            ];
            
            $currentLocation = $customerLocation;
        }

        // Sort by distance
        usort($routes, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $routes;
    }

    /**
     * Get customer location (simplified)
     */
    private function getCustomerLocation($customer)
    {
        // In a real application, you'd get actual coordinates
        // For now, return random coordinates
        return [rand(1, 100), rand(1, 100)];
    }

    /**
     * Calculate distance between two points
     */
    private function calculateDistance($point1, $point2)
    {
        return sqrt(pow($point2[0] - $point1[0], 2) + pow($point2[1] - $point1[1], 2));
    }
} 