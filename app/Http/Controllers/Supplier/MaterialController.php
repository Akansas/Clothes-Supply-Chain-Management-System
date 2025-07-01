<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $query = Product::where('supplier_id', $supplier->id);

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $materials = $query->latest()->paginate(15);

        return view('supplier.materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $supplier = $user->rawMaterialSupplier;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'material' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'material' => $request->material,
            'price' => $request->price,
            'cost' => $request->cost,
            'category' => $request->category,
            'unit' => $request->unit,
            'sku' => 'RM-' . time(),
            'supplier_id' => $supplier->id,
            'is_active' => $request->boolean('is_active'),
            'stock_quantity' => $request->stock_quantity,
        ]);

        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $material)
    {
        // Ensure the supplier owns this material
        if ($material->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }
        return view('supplier.materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $material)
    {
        // Ensure the supplier owns this material
        if ($material->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }
        return view('supplier.materials.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $material)
    {
        // Ensure the supplier owns this material
        if ($material->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $material->update($request->all());

        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $material)
    {
        // Ensure the supplier owns this material
        if ($material->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }
        
        $material->delete();

        return redirect()->route('supplier.materials.index')
            ->with('success', 'Material deleted successfully!');
    }

    /**
     * Show the form for editing the stock quantity.
     */
    public function showStockForm(Product $material)
    {
        if ($material->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }
        return view('supplier.materials.stock', compact('material'));
    }

    /**
     * Update the stock quantity.
     */
    public function updateStock(Request $request, Product $material)
    {
        if ($material->supplier_id !== auth()->user()->rawMaterialSupplier->id) {
            abort(403);
        }

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $material->update([
            'stock_quantity' => $request->stock_quantity,
        ]);

        return redirect()->route('supplier.materials.index')
            ->with('success', 'Stock updated successfully!');
    }
} 