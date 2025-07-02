<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

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
        ]);
        $warehouse = \App\Models\Warehouse::first();
        if ($warehouse) {
            \App\Models\Inventory::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'location_type' => 'warehouse',
                'location_id' => $warehouse->id,
                'quantity' => $quantity,
                'reserved_quantity' => 0,
                'available_quantity' => $quantity,
                'status' => 'active',
            ]);
        }
        return redirect()->route('manufacturer.dashboard')->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = ['T-Shirt', 'Shirt', 'Pants', 'Dress', 'Jacket', 'Other'];
        return view('manufacturer.products.edit', compact('product', 'categories'));
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
        ]);
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price' => $request->price,
        ];
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);
        return redirect()->route('manufacturer.dashboard')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('manufacturer.dashboard')->with('success', 'Product deleted successfully.');
    }
} 