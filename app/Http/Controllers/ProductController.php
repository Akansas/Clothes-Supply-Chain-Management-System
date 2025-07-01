<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Product::with(['manufacturer', 'supplier', 'inventories']);

        // Filter based on user role
        switch ($user->role->name) {
            case 'manufacturer':
                $query->where('manufacturer_id', $user->id);
                break;
            case 'vendor':
                $query->where('vendor_id', $user->vendor->id ?? 0);
                break;
            case 'supplier':
                $query->where('supplier_id', $user->rawMaterialSupplier->id ?? 0);
                break;
        }

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->whereHas('inventories', function ($q) {
                $q->where('quantity', '<=', DB::raw('min_stock_level'));
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Product::distinct()->pluck('category')->filter();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Product::distinct()->pluck('category')->filter();
        $manufacturers = User::whereHas('role', function ($q) {
            $q->where('name', 'manufacturer');
        })->get();
        $suppliers = User::whereHas('role', function ($q) {
            $q->where('name', 'raw_material_supplier');
        })->get();

        return view('products.create', compact('categories', 'manufacturers', 'suppliers'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku|max:255',
            'description' => 'nullable|string',
            'material' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'manufacturer_id' => 'nullable|exists:users,id',
            'supplier_id' => 'nullable|exists:raw_material_suppliers,id',
            'season' => 'nullable|string|max:255',
            'collection' => 'nullable|string|max:255',
            'fabric_type' => 'nullable|string|max:255',
            'care_instructions' => 'nullable|string|max:255',
            'sustainability_rating' => 'nullable|integer|min:1|max:5',
            'lead_time_days' => 'nullable|integer|min:0',
            'moq' => 'nullable|integer|min:1',
            'weight_kg' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');
        $data['is_active'] = true;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        $product = Product::create($data);

        return redirect()->route('products.show', $product->id)
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        $product = Product::with(['manufacturer', 'supplier', 'inventories.warehouse', 'inventories.retailStore'])
            ->findOrFail($id);
        
        $inventoryStats = $this->getInventoryStats($product);
        $recentOrders = $this->getRecentOrders($product);

        return view('products.show', compact('product', 'inventoryStats', 'recentOrders'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Product::distinct()->pluck('category')->filter();
        $manufacturers = User::whereHas('role', function ($q) {
            $q->where('name', 'manufacturer');
        })->get();
        $suppliers = User::whereHas('role', function ($q) {
            $q->where('name', 'raw_material_supplier');
        })->get();

        return view('products.edit', compact('product', 'categories', 'manufacturers', 'suppliers'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $id . '|max:255',
            'description' => 'nullable|string',
            'material' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'manufacturer_id' => 'nullable|exists:users,id',
            'supplier_id' => 'nullable|exists:raw_material_suppliers,id',
            'season' => 'nullable|string|max:255',
            'collection' => 'nullable|string|max:255',
            'fabric_type' => 'nullable|string|max:255',
            'care_instructions' => 'nullable|string|max:255',
            'sustainability_rating' => 'nullable|integer|min:1|max:5',
            'lead_time_days' => 'nullable|integer|min:0',
            'moq' => 'nullable|integer|min:1',
            'weight_kg' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_url) {
                Storage::delete(str_replace('/storage/', 'public/', $product->image_url));
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        $product->update($data);

        return redirect()->route('products.show', $product->id)
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Check if product has inventory
        if ($product->inventories()->where('quantity', '>', 0)->exists()) {
            return back()->with('error', 'Cannot delete product with existing inventory.');
        }

        // Check if product has orders
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Cannot delete product with existing orders.');
        }

        // Delete image if exists
        if ($product->image_url) {
            Storage::delete(str_replace('/storage/', 'public/', $product->image_url));
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle product active status
     */
    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $product->is_active,
            'message' => 'Product status updated successfully'
        ]);
    }

    /**
     * Get product analytics
     */
    public function analytics($id)
    {
        $product = Product::with(['inventories', 'orderItems.order'])->findOrFail($id);

        // Inventory analytics
        $totalStock = $product->inventories->sum('quantity');
        $totalValue = $totalStock * $product->cost;
        $lowStockLocations = $product->inventories->where('quantity', '<=', 'min_stock_level');

        // Sales analytics
        $totalSold = $product->orderItems->sum('quantity');
        $totalRevenue = $product->orderItems->sum('total_price');
        $avgOrderQuantity = $product->orderItems->count() > 0 ? $totalSold / $product->orderItems->count() : 0;

        // Monthly sales
        $monthlySales = $product->orderItems()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total_price) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('products.analytics', compact(
            'product',
            'totalStock',
            'totalValue',
            'lowStockLocations',
            'totalSold',
            'totalRevenue',
            'avgOrderQuantity',
            'monthlySales'
        ));
    }

    /**
     * Bulk product operations
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id'
        ]);

        $products = Product::whereIn('id', $request->product_ids);

        switch ($request->action) {
            case 'activate':
                $products->update(['is_active' => true]);
                $message = 'Products activated successfully!';
                break;
            case 'deactivate':
                $products->update(['is_active' => false]);
                $message = 'Products deactivated successfully!';
                break;
            case 'delete':
                // Check if any products have inventory or orders
                $productsWithData = $products->where(function ($q) {
                    $q->whereHas('inventories', function ($iq) {
                        $iq->where('quantity', '>', 0);
                    })->orWhereHas('orderItems');
                })->count();

                if ($productsWithData > 0) {
                    return back()->with('error', 'Some products cannot be deleted due to existing inventory or orders.');
                }

                $products->delete();
                $message = 'Products deleted successfully!';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Get inventory statistics for a product
     */
    private function getInventoryStats($product)
    {
        $inventories = $product->inventories;
        
        return [
            'total_quantity' => $inventories->sum('quantity'),
            'total_value' => $inventories->sum('quantity') * $product->cost,
            'locations_count' => $inventories->count(),
            'low_stock_locations' => $inventories->where('quantity', '<=', 'min_stock_level')->count(),
            'out_of_stock_locations' => $inventories->where('quantity', 0)->count(),
        ];
    }

    /**
     * Get recent orders for a product
     */
    private function getRecentOrders($product)
    {
        return $product->orderItems()
            ->with(['order.customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
} 