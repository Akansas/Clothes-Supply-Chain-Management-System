<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            'status' => 'required|in:confirmed,rejected,shipped',
        ]);

        $order->update(['status' => $request->status]);

        // Potentially trigger notifications or other events here

        return redirect()->route('supplier.orders.show', $order)
            ->with('success', 'Order status updated successfully!');
    }
} 