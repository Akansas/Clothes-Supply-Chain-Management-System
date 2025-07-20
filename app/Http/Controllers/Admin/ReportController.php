<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;

class ReportController extends Controller
{
    public function download(Request $request, $type)
    {
        $users = User::all();
        $orders = Order::all();
        $products = Product::all();
        $inventory = Inventory::all();
        
        $data = [
            'users' => $users,
            'orders' => $orders,
            'products' => $products,
            'inventory' => $inventory,
            'totalUsers' => $users->count(),
            'totalOrders' => $orders->count(),
            'totalProducts' => $products->count(),
            'totalRevenue' => $orders->where('status', 'delivered')->sum('total_amount'),
            'reportDate' => now()->format('Y-m-d H:i:s')
        ];

        if ($type === 'pdf') {
            // Generate actual PDF file
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.admin.pdf', $data);
            return $pdf->download('admin_report_' . now()->format('Y-m-d') . '.pdf');
        }

        return back()->with('error', 'Invalid report type');
    }
} 