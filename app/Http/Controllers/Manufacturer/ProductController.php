<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function create()
    {
        $categories = ['T-Shirt', 'Shirt', 'Pants', 'Dress', 'Jacket', 'Other'];
        return view('manufacturer.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        $sku = 'SKU-' . strtoupper(uniqid());
        $material = $request->input('material', 'Unknown');
        $cost = $request->input('cost', 0);
        $unit = $request->input('unit', 'pcs');
        $quantity = $request->input('quantity', 0);
        $manufacturer = \App\Models\Manufacturer::first();
        $userId = $manufacturer ? $manufacturer->user_id : null;
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price' => $request->price,
            'image' => $imagePath,
            'sku' => $sku,
            'material' => $material,
            'cost' => $cost,
            'unit' => $unit,
            'manufacturer_id' => $userId,
            'supplier_id' => null,
            'is_active' => true,
        ]);
        // Ensure a warehouse exists
        $warehouse = \App\Models\Warehouse::first();
        if (!$warehouse) {
            $warehouse = \App\Models\Warehouse::create([
                'name' => 'Default Warehouse',
                'location' => 'Default Location',
                'status' => 'active',
            ]);
        }
        // Always create an inventory record for the product
        \App\Models\Inventory::updateOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'location_type' => 'warehouse',
                'location_id' => $warehouse->id,
                'quantity' => $quantity,
                'reserved_quantity' => 0,
                'available_quantity' => $quantity,
                'status' => 'active',
            ]
        );
        return redirect()->route('manufacturer.dashboard')->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = ['T-Shirt', 'Shirt', 'Pants', 'Dress', 'Jacket', 'Other'];
        $inventory = $product->inventory;
        $quantity = $inventory ? $inventory->quantity : 0;
        return view('manufacturer.products.edit', compact('product', 'categories', 'quantity'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
        ]);
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price' => $request->price,
            'min_stock_level' => $request->min_stock_level,
        ];
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);
        // Ensure a warehouse exists
        $warehouse = \App\Models\Warehouse::first();
        if (!$warehouse) {
            $warehouse = \App\Models\Warehouse::create([
                'name' => 'Default Warehouse',
                'location' => 'Default Location',
                'status' => 'active',
            ]);
        }
        // Always update or create the inventory record for the product
        $inventory = \App\Models\Inventory::updateOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'location_type' => 'warehouse',
                'location_id' => $warehouse->id,
                'quantity' => $request->quantity,
                'available_quantity' => $request->quantity,
                'status' => 'active',
            ]
        );
        $reason = $request->input('reason', '-');
        // Log the change
        \App\Models\InventoryChangeLog::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'item_type' => 'finished_product',
            'old_quantity' => $product->inventory->quantity ?? 0,
            'new_quantity' => $request->quantity,
            'note' => $reason,
            'change_type' => 'update',
        ]);
        return redirect()->route('manufacturer.dashboard')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('manufacturer.dashboard')->with('success', 'Product deleted successfully.');
    }
} 