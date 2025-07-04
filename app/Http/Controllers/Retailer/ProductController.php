<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Show all finished products from manufacturers
    public function browseManufacturerProducts(Request $request)
    {
        $manufacturer = \App\Models\Manufacturer::first();
        $query = Product::with('manufacturer')
            ->where('manufacturer_id', $manufacturer ? $manufacturer->id : null)
            ->where('is_active', true)
            ->whereNull('supplier_id');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $products = $query->paginate(12);
        $categories = Product::whereNotNull('manufacturer_id')->distinct()->pluck('category')->filter();
        return view('retailer.products.browse', compact('products', 'categories'));
    }

    // Show form to place a production order for a manufacturer product
    public function createProductionOrder($productId)
    {
        $product = Product::with('manufacturer')->findOrFail($productId);
        return view('retailer.production-orders.create', compact('product'));
    }

    // Store the production order
    public function storeProductionOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'shipping_country' => 'required|string',
        ]);
        $product = Product::findOrFail($request->product_id);
        $user = Auth::user();
        $manufacturerId = \App\Models\Manufacturer::first()->id;
        $order = Order::create([
            'order_number' => 'PO-' . time(),
            'product_id' => $product->id,
            'design_id' => $product->design_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'due_date' => $request->due_date,
            'retailer_id' => $user->id,
            'user_id' => $user->id,
            'manufacturer_id' => $manufacturerId,
            'notes' => $request->notes,
            'total_amount' => $product->price * $request->quantity,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_zip' => $request->shipping_zip,
            'shipping_country' => $request->shipping_country,
        ]);
        // Create an order item for this order
        $order->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_price' => $product->price,
            'total_price' => $product->price * $request->quantity,
        ]);
        // Create a ProductionOrder for the manufacturer dashboard
        \App\Models\ProductionOrder::create([
            'manufacturer_id' => $manufacturerId,
            'retailer_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);
        return redirect()->route('retailer.products.browse')->with('success', 'Production order placed successfully!');
    }

    // List all production orders placed by the current retailer
    public function productionOrdersIndex()
    {
        $user = Auth::user();
        $productionOrders = Order::with(['product.manufacturer'])
            ->where('retailer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $allProductionOrders = null;
        if ($productionOrders->total() === 0) {
            $allProductionOrders = Order::with('product')->orderBy('created_at', 'desc')->get();
        }
        return view('retailer.production-orders.index', compact('productionOrders', 'allProductionOrders'));
    }
} 