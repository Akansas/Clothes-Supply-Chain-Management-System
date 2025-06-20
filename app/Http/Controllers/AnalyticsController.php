<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class AnalyticsController extends Controller
{
    public function sales()
    {
        // Get all products
        $allProducts = \App\Models\Product::all();

        // Build best-selling products data (all products, zero if no sales)
        $bestSellingLabels = [];
        $bestSellingData = [];
        foreach ($allProducts as $product) {
            $bestSellingLabels[] = $product->name;
            $bestSellingData[] = $product->orders->sum('quantity');
        }

        // Aggregate sales by product (for table)
        $salesData = Order::selectRaw('product_id, SUM(quantity) as total_sales, SUM(total) as total_revenue')
            ->groupBy('product_id')
            ->with('product')
            ->get();

        // Sales over time (by date, using total)
        $salesOverTime = Order::selectRaw('DATE(created_at) as date, SUM(total) as total_revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Summary cards
        $totalRevenue = Order::sum('total');
        $totalOrders = Order::count();
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // Product performance table
        $productPerformance = \App\Models\Product::with(['orders' => function($q) {
            $q->selectRaw('product_id, SUM(quantity) as total_sales, SUM(total) as total_revenue')
              ->groupBy('product_id');
        }])->get();

        return view('retailer.analytics', compact('salesData', 'salesOverTime', 'totalRevenue', 'totalOrders', 'averageOrderValue', 'productPerformance', 'bestSellingLabels', 'bestSellingData'));
    }
} 