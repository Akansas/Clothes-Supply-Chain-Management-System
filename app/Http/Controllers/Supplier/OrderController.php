<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $query = Order::where('supplier_id', $supplier->id)
            ->with(['manufacturer', 'orderItems.product']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15);

        return view('supplier.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Ensure the supplier owns this order
        if ($order->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }
        
        $order->load(['manufacturer', 'orderItems.product', 'deliveries']);

        return view('supplier.orders.show', compact('order'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if ($order->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:confirmed,rejected,shipped,approved,delivered',
        ]);

        $order->update(['status' => $request->status]);

        // Potentially trigger notifications or other events here

        return redirect()->route('supplier.orders.index')
            ->with('success', 'Order status updated successfully!');
    }

    /**
     * Show the form to assign delivery personnel and ship the order.
     */
    public function assignDeliveryPersonnel(Order $order)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        if ($order->supplier_id !== $supplier->id) abort(403);
        $deliveryPersonnel = User::whereHas('role', function($q) {
            $q->where('name', 'delivery_personnel');
        })->get();
        return view('supplier.orders.assign_delivery', compact('order', 'deliveryPersonnel'));
    }

    /**
     * Handle the shipping of the order and assignment of delivery personnel.
     */
    public function shipOrder(Request $request, Order $order)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        if ($order->supplier_id !== $supplier->id) abort(403);
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);
        // If order is still pending, approve it
        if ($order->status === 'pending') {
            $order->status = 'confirmed';
            $order->save();
        }
        // Create delivery record
        $delivery = Delivery::create([
            'order_id' => $order->id,
            'supplier_id' => $supplier->id,
            'driver_id' => $request->driver_id,
            'status' => 'in_transit',
            'tracking_number' => strtoupper('TRK' . uniqid()),
        ]);
        // Update order status
        $order->update(['status' => 'shipped']);
        return redirect()->route('supplier.orders.show', $order)->with('success', 'Order shipped and delivery personnel assigned.');
    }

    /**
     * Cancel a shipment (delivery) if not delivered or already cancelled.
     */
    public function cancelDelivery(Delivery $delivery)
    {
        if (in_array($delivery->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel a delivered or already cancelled shipment.');
        }
        $delivery->update(['status' => 'cancelled']);
        // Optionally, update the related order status if needed
        return back()->with('success', 'Shipment cancelled successfully.');
    }
} 