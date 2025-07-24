<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function download(Request $request, $type)
    {
        $user = Auth::user();
        $supplier = $user->rawMaterialSupplier;
        
        if (!$supplier) {
            return back()->with('error', 'Supplier profile not found.');
        }

        // Use the same logic as dashboard controller
        $stats = [
            'total_materials' => Product::where('supplier_id', $supplier->id)->count(),
            'active_orders' => Order::where('supplier_id', $supplier->id)
                ->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed_deliveries' => Order::where('supplier_id', $supplier->id)
                ->where('status', 'delivered')->count(),
            'approved_orders' => Order::where('supplier_id', $supplier->id)
                ->where('status', 'approved')->count(),
            'cancelled_orders' => Order::where('supplier_id', $supplier->id)
                ->where('status', 'cancelled')->count(),
            'rejected_orders' => Order::where('supplier_id', $supplier->id)
                ->where('status', 'rejected')->count(),
            'total_revenue' => Order::where('supplier_id', $supplier->id)
                ->where('status', 'delivered')->sum('total_amount'),
            'total_cost' => Product::where('supplier_id', $supplier->id)
                ->sum(DB::raw('price * stock_quantity')),
            'monthly_orders' => Order::where('supplier_id', $supplier->id)
                ->whereMonth('created_at', now()->month)->count(),
        ];

        // Get the same data as dashboard
        $inventory = Product::where('supplier_id', $supplier->id)->get();
        $pendingOrders = Order::where('supplier_id', $supplier->id)->where('status', 'pending')->get();
        $completedOrders = Order::where('supplier_id', $supplier->id)->where('status', 'delivered')->get();
        
        $data = [
            'supplier' => $supplier,
            'user' => $user,
            'inventory' => $inventory,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
            'stats' => $stats,
            'totalRevenue' => $stats['total_revenue'],
            'totalCost' => $stats['total_cost'],
            'reportDate' => now()->format('Y-m-d H:i:s')
        ];

        if ($type === 'pdf') {
            // Generate actual PDF file
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.supplier.pdf', $data);
            return $pdf->download('supplier_report_' . now()->format('Y-m-d') . '.pdf');
        }

        return back()->with('error', 'Invalid report type');
    }
} 