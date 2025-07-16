<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryChangeLog;

class InventoryController extends Controller
{
    /**
     * Show inventory overview with two tables: raw materials and finished products.
     */
    public function index()
    {
        $user = auth()->user();
        $manufacturerId = $user->manufacturer_id;
        // Raw materials: Only those with delivered orders for this manufacturer
        $rawMaterials = Product::where('manufacturer_id', $manufacturerId)
            ->whereNotNull('supplier_id')
            ->whereHas('orderItems.order', function ($query) use ($user) {
                $query->where('status', 'delivered')
                      ->where('user_id', $user->id);
            })
            ->get();
        // Attach delivered quantity to each raw material
        foreach ($rawMaterials as $material) {
            $deliveredQty = $material->orderItems()
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('status', 'delivered')
                          ->where('user_id', $user->id);
                })
                ->sum('quantity');
            $material->delivered_quantity = $deliveredQty;
        }
        // Finished products: match dashboard logic
        $manufacturer = \App\Models\Manufacturer::first();
        $finishedProducts = Product::where('manufacturer_id', $manufacturer ? $manufacturer->id : null)
            ->whereNull('supplier_id')
            ->with('inventory')
            ->get();
        return view('manufacturer.inventory.index', compact('rawMaterials', 'finishedProducts'));
    }

    /**
     * Show form to update stock for a raw material.
     */
    public function editRawMaterial($id)
    {
        $user = auth()->user();
        $manufacturerId = $user->manufacturer_id;
        $material = Product::where('manufacturer_id', $manufacturerId)
            ->whereNotNull('supplier_id')
            ->findOrFail($id);
        return view('manufacturer.inventory.edit_raw_material', compact('material'));
    }

    /**
     * Update stock for a raw material.
     */
    public function updateRawMaterial(Request $request, $id)
    {
        $user = auth()->user();
        $manufacturerId = $user->manufacturer_id;
        $material = Product::where('manufacturer_id', $manufacturerId)
            ->whereNotNull('supplier_id')
            ->findOrFail($id);
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
        ]);
        $oldQty = $material->stock_quantity;
        $material->stock_quantity = $request->stock_quantity;
        $material->min_stock_level = $request->min_stock_level;
        $material->save();
        InventoryChangeLog::create([
            'product_id' => $material->id,
            'item_type' => 'raw_material',
            'change_type' => 'update',
            'old_quantity' => $oldQty,
            'new_quantity' => $material->stock_quantity,
            'user_id' => $user->id,
            'note' => null,
        ]);
        return redirect()->route('manufacturer.inventory.index')->with('success', 'Raw material stock updated!');
    }

    /**
     * Show form to update stock for a finished product.
     */
    public function editFinishedProduct($id)
    {
        $user = auth()->user();
        $manufacturerId = $user->manufacturer_id;
        $product = Product::where('manufacturer_id', $manufacturerId)
            ->whereNull('supplier_id')
            ->findOrFail($id);
        return view('manufacturer.inventory.edit_finished_product', compact('product'));
    }

    /**
     * Update stock for a finished product.
     */
    public function updateFinishedProduct(Request $request, $id)
    {
        $user = auth()->user();
        $manufacturerId = $user->manufacturer_id;
        $product = Product::where('manufacturer_id', $manufacturerId)
            ->whereNull('supplier_id')
            ->findOrFail($id);
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);
        $oldQty = $product->stock_quantity;
        $product->stock_quantity = $request->stock_quantity;
        $product->save();
        InventoryChangeLog::create([
            'product_id' => $product->id,
            'item_type' => 'finished_product',
            'change_type' => 'update',
            'old_quantity' => $oldQty,
            'new_quantity' => $product->stock_quantity,
            'user_id' => $user->id,
            'note' => null,
        ]);
        return redirect()->route('manufacturer.inventory.index')->with('success', 'Finished product stock updated!');
    }
} 