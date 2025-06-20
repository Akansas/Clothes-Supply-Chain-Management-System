<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('product')->get();
        return view('retailer.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('retailer.orders.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            
            // Check if we have enough stock
            if ($product->stock < $request->quantity) {
                return back()->withErrors(['quantity' => 'Not enough stock available.'])->withInput();
            }

            // Create the order
            $order = Order::create($request->only('product_id', 'quantity', 'customer_name'));

            // Update stock
            $newStock = $product->stock - $request->quantity;
            $product->update(['stock' => $newStock]);

            // Send notifications
            $this->notifyAdminsAndRetailers(new NewOrderNotification($order));
            
            // Check for low stock
            if ($newStock <= 20) {
                $this->notifyAdminsAndRetailers(new LowStockNotification($product));
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to place order. Please try again.'])->withInput();
        }
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
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $products = Product::all();
        return view('retailer.orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required',
            'customer_name' => 'required',
        ]);
        $order = Order::findOrFail($id);
        $order->update($request->only('product_id', 'quantity', 'status', 'customer_name'));
        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    private function notifyAdminsAndRetailers($notification)
    {
        $users = User::where('id', '!=', auth()->id())->get();
        Notification::send($users, $notification);
    }
}
