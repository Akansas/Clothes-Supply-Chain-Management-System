<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * List all deliveries for the supplier's orders.
     */
    public function index()
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        $deliveries = Delivery::whereHas('order', function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id);
        })->with(['order', 'driver'])->latest()->paginate(15);
        return view('supplier.deliveries.index', compact('deliveries'));
    }

    /**
     * Show details for a delivery.
     */
    public function show(Delivery $delivery)
    {
        $supplierId = auth()->user()->rawMaterialSupplier->id;
        if ($delivery->order->supplier_id !== $supplierId) {
            abort(403);
        }
        $delivery->load(['order', 'driver']);
        return view('supplier.deliveries.show', compact('delivery'));
    }

    /**
     * Update the status of a delivery.
     */
    public function updateStatus(Request $request, Delivery $delivery)
    {
        $supplierId = auth()->user()->rawMaterialSupplier->id;
        if ($delivery->order->supplier_id !== $supplierId) {
            abort(403);
        }
        $request->validate([
            'status' => 'required|in:pending,in_transit,delivered,failed',
        ]);
        $delivery->update(['status' => $request->status]);
        return redirect()->route('supplier.deliveries.show', $delivery)
            ->with('success', 'Delivery status updated successfully!');
    }
} 