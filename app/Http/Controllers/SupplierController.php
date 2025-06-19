<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Inventory;

class SupplierController extends Controller
{
    public function dashboard()
    {
        $orders = Order::where('supplier_id', auth()->user()->supplier->id)
                      ->orderBy('created_at', 'desc')
                      ->take(5)
                      ->get();
        
        $shipments = Shipment::where('supplier_id', auth()->user()->supplier->id)
                            ->where('status', 'pending')
                            ->get();
        
        return view('supplier.dashboard', compact('orders', 'shipments'));
    }

    public function orders()
    {
        $orders = Order::where('supplier_id', auth()->user()->supplier->id)
                      ->with(['items.product'])
                      ->paginate(10);
        
        return view('supplier.orders', compact('orders'));
    }

    public function shipments()
    {
        $shipments = Shipment::where('supplier_id', auth()->user()->supplier->id)
                            ->with(['items.product'])
                            ->paginate(10);
        
        return view('supplier.shipments', compact('shipments'));
    }

    public function inventory()
    {
        $inventory = Inventory::where('supplier_id', auth()->user()->supplier->id)
                            ->with(['product'])
                            ->get();
        
        return view('supplier.inventory', compact('inventory'));
    }

    public function profile()
    {
        $supplier = auth()->user()->supplier;
        return view('supplier.profile', compact('supplier'));
    }
}