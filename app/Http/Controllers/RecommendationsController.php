<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class RecommendationsController extends Controller
{
    public function index()
    {
        // Low stock: stock <= 20
        $lowStock = Product::where('stock', '<=', 20)->get();

        // Best-sellers: top 3 by sales
        $bestSellers = Product::withSum('orders as total_sales', 'quantity')
            ->orderByDesc('total_sales')
            ->take(3)
            ->get();

        // Slow-moving: stock > 0 and no sales in last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        $slowMoving = Product::where('stock', '>', 0)
            ->whereDoesntHave('orders', function($q) use ($thirtyDaysAgo) {
                $q->where('created_at', '>=', $thirtyDaysAgo);
            })->get();

        return view('retailer.recommendations', compact(
            'lowStock',
            'bestSellers',
            'slowMoving'
        ));
    }
} 