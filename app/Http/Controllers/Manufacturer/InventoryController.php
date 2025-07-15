<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manufacturerId = auth()->user()->manufacturer_id;
        $rawMaterials = \App\Models\RawMaterial::all();
        $suppliers = \App\Models\Supplier::all();
        $incomingShipments = \App\Models\IncomingShipment::all();
        $wip = \App\Models\WorkInProgress::all();
        $finishedGoods = \App\Models\FinishedGood::all();
        $inventoryLogs = \App\Models\InventoryLog::orderBy('created_at', 'desc')->limit(20)->get();
        return view('manufacturer.inventory.index', compact('rawMaterials', 'suppliers', 'incomingShipments', 'wip', 'finishedGoods', 'inventoryLogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function createRawMaterial()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('manufacturer.inventory.create_raw_material', compact('suppliers'));
    }

    public function storeRawMaterial(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'quantity' => 'required|integer',
            'reorder_level' => 'required|integer',
            'unit' => 'required',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required',
        ]);
        \App\Models\RawMaterial::create($data);
        return redirect()->route('inventory.index')->with('success', 'Raw material added successfully.');
    }

    public function editRawMaterial($id)
    {
        $rawMaterial = \App\Models\RawMaterial::findOrFail($id);
        $suppliers = \App\Models\Supplier::all();
        return view('manufacturer.inventory.edit_raw_material', compact('rawMaterial', 'suppliers'));
    }

    public function updateRawMaterial(Request $request, $id)
    {
        $rawMaterial = \App\Models\RawMaterial::findOrFail($id);
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'quantity' => 'required|integer',
            'reorder_level' => 'required|integer',
            'unit' => 'required',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required',
        ]);
        $rawMaterial->update($data);
        return redirect()->route('inventory.index')->with('success', 'Raw material updated successfully.');
    }

    public function destroyRawMaterial($id)
    {
        $rawMaterial = \App\Models\RawMaterial::findOrFail($id);
        $rawMaterial->delete();
        return redirect()->route('inventory.index')->with('success', 'Raw material deleted successfully.');
    }

    public function createSupplier()
    {
        return view('manufacturer.inventory.create_supplier');
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'contact_info' => 'nullable',
            'address' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
        ]);
        \App\Models\Supplier::create($data);
        return redirect()->route('inventory.index')->with('success', 'Supplier added successfully.');
    }

    public function editSupplier($id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        return view('manufacturer.inventory.edit_supplier', compact('supplier'));
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $data = $request->validate([
            'name' => 'required',
            'contact_info' => 'nullable',
            'address' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
        ]);
        $supplier->update($data);
        return redirect()->route('inventory.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroySupplier($id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('inventory.index')->with('success', 'Supplier deleted successfully.');
    }

    public function createIncomingShipment()
    {
        $rawMaterials = \App\Models\RawMaterial::all();
        $suppliers = \App\Models\Supplier::all();
        return view('manufacturer.inventory.create_incoming_shipment', compact('rawMaterials', 'suppliers'));
    }

    public function storeIncomingShipment(Request $request)
    {
        $data = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer',
            'expected_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'status' => 'required',
        ]);
        \App\Models\IncomingShipment::create($data);
        return redirect()->route('inventory.index')->with('success', 'Incoming shipment added successfully.');
    }

    public function editIncomingShipment($id)
    {
        $shipment = \App\Models\IncomingShipment::findOrFail($id);
        $rawMaterials = \App\Models\RawMaterial::all();
        $suppliers = \App\Models\Supplier::all();
        return view('manufacturer.inventory.edit_incoming_shipment', compact('shipment', 'rawMaterials', 'suppliers'));
    }

    public function updateIncomingShipment(Request $request, $id)
    {
        $shipment = \App\Models\IncomingShipment::findOrFail($id);
        $data = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer',
            'expected_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'status' => 'required',
        ]);
        $shipment->update($data);
        return redirect()->route('inventory.index')->with('success', 'Incoming shipment updated successfully.');
    }

    public function destroyIncomingShipment($id)
    {
        $shipment = \App\Models\IncomingShipment::findOrFail($id);
        $shipment->delete();
        return redirect()->route('inventory.index')->with('success', 'Incoming shipment deleted successfully.');
    }

    public function showAdjustmentForm()
    {
        $rawMaterials = \App\Models\RawMaterial::all();
        $finishedGoods = \App\Models\FinishedGood::all();
        return view('manufacturer.inventory.create_adjustment', compact('rawMaterials', 'finishedGoods'));
    }

    public function storeAdjustment(Request $request)
    {
        $data = $request->validate([
            'raw_material_id' => 'nullable|exists:raw_materials,id',
            'finished_good_id' => 'nullable|exists:finished_goods,id',
            'adjustment_type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);
        $data['user_id'] = auth()->id();
        
        // Update inventory
        if ($data['raw_material_id']) {
            $rawMaterial = \App\Models\RawMaterial::find($data['raw_material_id']);
            if ($data['adjustment_type'] === 'increase') {
                $rawMaterial->quantity += $data['quantity'];
            } else {
                $rawMaterial->quantity -= $data['quantity'];
            }
            $rawMaterial->save();
        } elseif ($data['finished_good_id']) {
            $finishedGood = \App\Models\FinishedGood::find($data['finished_good_id']);
            if ($data['adjustment_type'] === 'increase') {
                $finishedGood->quantity += $data['quantity'];
            } else {
                $finishedGood->quantity -= $data['quantity'];
            }
            $finishedGood->save();
        }
        \App\Models\InventoryAdjustment::create($data);
        return redirect()->route('inventory.index')->with('success', 'Inventory adjustment recorded successfully.');
    }

    public function listAdjustments()
    {
        $adjustments = \App\Models\InventoryAdjustment::with(['rawMaterial', 'finishedGood', 'user'])->orderBy('created_at', 'desc')->paginate(20);
        return view('manufacturer.inventory.adjustments', compact('adjustments'));
    }
}
