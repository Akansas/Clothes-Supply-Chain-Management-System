<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Product;
use Barryvdh\DomPDF\PDF;

class ReportController extends Controller
{
    public function download(Request $request, $type)
    {
        $retailer = Auth::user();
        $retailStore = $retailer->managedRetailStore;
        
        // Use the same logic as dashboard controller
        $orders = Order::where('retailer_id', $retailer->id)->get();
        $inventory = Inventory::where('retail_store_id', $retailStore->id)->get();
        $products = Product::whereHas('inventory', function($q) use ($retailStore) {
            $q->where('retail_store_id', $retailStore->id);
        })->get();
        
        // Calculate stats exactly like dashboard controller
        $stats = [
            'total_orders' => Order::where('retailer_id', $retailer->id)->count(),
            'pending_orders' => Order::where('retailer_id', $retailer->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('retailer_id', $retailer->id)->where('status', 'delivered')->count(),
            'approved_orders' => Order::where('retailer_id', $retailer->id)->where('status', 'approved')->count(),
            'cancelled_orders' => Order::where('retailer_id', $retailer->id)->where('status', 'cancelled')->count(),
            'rejected_orders' => Order::where('retailer_id', $retailer->id)->where('status', 'rejected')->count(),
            'total_cost' => Order::where('retailer_id', $retailer->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'low_stock_products' => Inventory::where('retail_store_id', $retailStore->id)->where('quantity', '<', 10)->count(),
            'products_count' => Inventory::where('retail_store_id', $retailStore->id)->count(),
            'monthly_orders' => Order::where('retailer_id', $retailer->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];
        
        $data = [
            'retailer' => $retailer,
            'retailStore' => $retailStore,
            'orders' => $orders,
            'inventory' => $inventory,
            'products' => $products,
            'stats' => $stats,
            'totalOrders' => $stats['total_orders'],
            'totalRevenue' => $stats['total_cost'], // This is the total cost from dashboard
            'totalCost' => $stats['total_cost'],
            'reportDate' => now()->format('Y-m-d H:i:s')
        ];

        if ($type === 'pdf') {
            // Generate actual PDF file
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.retailer.pdf', $data);
            return $pdf->download('retailer_report_' . now()->format('Y-m-d') . '.pdf');
        }

        return back()->with('error', 'Invalid report type');
    }
} 