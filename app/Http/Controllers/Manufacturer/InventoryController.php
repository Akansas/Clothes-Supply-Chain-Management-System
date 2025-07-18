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
        // Show all raw materials from delivered purchase orders (from this manufacturer to any supplier)
        $deliveredOrderItems = \App\Models\OrderItem::whereHas('order', function ($query) use ($user) {
            $query->where('status', 'delivered')
                  ->where('user_id', $user->id)
                  ->whereNotNull('supplier_id');
        })
        ->with('product')
        ->get();

        // Group by product and sum quantities
        $rawMaterials = $deliveredOrderItems->groupBy('product_id')->map(function($items) {
            $product = $items->first()->product;
            return (object) [
                'id' => $product->id,
                'name' => $product->name,
                'unit' => $product->unit ?? '-',
                'min_stock_level' => $product->min_stock_level ?? '-',
                'delivered_quantity' => $items->sum('quantity'),
            ];
        })->values();
        // Finished products: only those added by this manufacturer
        $finishedProducts = Product::where('manufacturer_id', $user->id)
            ->where('type', 'finished_product')
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
        // Allow editing any product with a supplier (raw material), regardless of manufacturer_id
        $material = \App\Models\Product::whereNotNull('supplier_id')->findOrFail($id);
        return view('manufacturer.inventory.edit_raw_material', compact('material'));
    }

    /**
     * Update stock for a raw material.
     */
    public function updateRawMaterial(Request $request, $id)
    {
        $user = auth()->user();
        // Allow updating any product with a supplier (raw material), regardless of manufacturer_id
        $material = Product::whereNotNull('supplier_id')->findOrFail($id);
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'note' => 'required|string',
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
            'note' => $request->note,
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