<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\ProductionOrder;

class ReportController extends Controller
{
    public function download(Request $request, $type)
    {
        $user = Auth::user();
        $manufacturerId = $user->id;
        
        // Use the exact same logic as dashboard controller
        // Purchase Orders (from suppliers) - using user_id
        $purchaseOrderStatuses = ['pending', 'approved', 'rejected', 'delivered', 'cancelled'];
        $purchaseOrdersStats = [];
        foreach ($purchaseOrderStatuses as $status) {
            $purchaseOrdersStats[$status] = Order::where('user_id', $user->id)->where('status', $status)->count();
        }
        
        // Retailer Orders (to retailers) - using manufacturer_id and retailer_id
        $retailerOrderStatuses = ['pending', 'approved', 'rejected', 'delivered', 'cancelled'];
        $retailerOrdersStats = [];
        foreach ($retailerOrderStatuses as $status) {
            $retailerOrdersStats[$status] = ProductionOrder::where('manufacturer_id', $manufacturerId)
                ->whereNotNull('retailer_id')
                ->where('status', $status)
                ->count();
        }
        
        // Total Cost (sum of total_amount for delivered purchase orders)
        $totalCost = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->sum('total_amount');
        
        // Total Revenue (from delivered retailer orders)
        $totalRevenue = ProductionOrder::where('manufacturer_id', $manufacturerId)
            ->whereNotNull('retailer_id')
            ->where('status', 'delivered')
            ->with('product')
            ->get()
            ->sum(function($order) {
                return ($order->product && $order->quantity) ? $order->product->price * $order->quantity : 0;
            });
        
        // Get recent purchase orders
        $recentPurchaseOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->with(['supplier.user'])
            ->get();
            
        // Get recent retailer orders
        $recentRetailerOrders = ProductionOrder::where('manufacturer_id', $manufacturerId)
            ->whereNotNull('retailer_id')
            ->latest()
            ->take(5)
            ->with(['product', 'retailer'])
            ->get();
        
        $data = [
            'manufacturer' => $user,
            'purchaseOrdersStats' => $purchaseOrdersStats,
            'retailerOrdersStats' => $retailerOrdersStats,
            'totalCost' => $totalCost,
            'totalRevenue' => $totalRevenue,
            'recentPurchaseOrders' => $recentPurchaseOrders,
            'recentRetailerOrders' => $recentRetailerOrders,
            'reportDate' => now()->format('Y-m-d H:i:s')
        ];

        if ($type === 'pdf') {
            // Generate actual PDF file
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.manufacturer.pdf', $data);
            return $pdf->download('manufacturer_report_' . now()->format('Y-m-d') . '.pdf');
        }

        return back()->with('error', 'Invalid report type');
    }
} 