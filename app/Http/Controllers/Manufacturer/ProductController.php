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
        $manufacturer = \App\Models\Manufacturer::first();
        if (!$manufacturer) {
            return redirect()->back()->with('error', 'No manufacturer profile found in the system.');
        }
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
        // Generate a unique SKU
        $sku = 'SKU-' . strtoupper(uniqid());
        // Provide a default value for material
        $material = $request->input('material', 'Unknown');
        // Provide a default value for cost
        $cost = $request->input('cost', 0);
        // Provide a default value for unit
        $unit = $request->input('unit', 'pcs');
        $quantity = $request->input('quantity', 0);
        $product = Product::create([
            'manufacturer_id' => $manufacturer->id,
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
        // Save quantity in inventory for the first warehouse
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
        $manufacturer = \App\Models\Manufacturer::first();
        if (!$manufacturer) {
            return redirect()->back()->with('error', 'No manufacturer profile found in the system.');
        }
        $product = Product::where('manufacturer_id', $manufacturer->id)->findOrFail($id);
        $categories = ['T-Shirt', 'Shirt', 'Pants', 'Dress', 'Jacket', 'Other'];
        return view('manufacturer.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $manufacturer = \App\Models\Manufacturer::first();
        if (!$manufacturer) {
            return redirect()->back()->with('error', 'No manufacturer profile found in the system.');
        }
        $product = Product::where('manufacturer_id', $manufacturer->id)->findOrFail($id);
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
        $manufacturer = \App\Models\Manufacturer::first();
        if (!$manufacturer) {
            return redirect()->back()->with('error', 'No manufacturer profile found in the system.');
        }
        $product = Product::where('manufacturer_id', $manufacturer->id)->findOrFail($id);
        $product->delete();
        return redirect()->route('manufacturer.dashboard')->with('success', 'Product deleted successfully.');
    }
} 