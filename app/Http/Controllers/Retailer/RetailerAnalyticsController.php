<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Support\Carbon;

class RetailerAnalyticsController extends Controller
{
    // 1. Sales Insights Panel
    public function salesInsights(Request $request)
    {
        $retailerId = $request->user()->id;
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // Total Sales (Daily/Weekly)
        $dailySales = Order::where('retailer_id', $retailerId)
            ->whereDate('created_at', $today)
            ->where('status', 'delivered')
            ->sum('total_amount');
        $weeklySales = Order::where('retailer_id', $retailerId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('status', 'delivered')
            ->sum('total_amount');

        // Top-Selling SKUs
        $topSellingSkus = OrderItem::whereHas('order', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product:sku,id,name')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'sku' => $item->product->sku ?? null,
                    'name' => $item->product->name ?? null,
                    'total_sold' => $item->total_sold,
                ];
            });

        // Sales by Channel (in-store vs online)
        $inStoreSales = Order::where('retailer_id', $retailerId)
            ->whereNotNull('retail_store_id')
            ->where('status', 'delivered')
            ->sum('total_amount');
        $onlineSales = Order::where('retailer_id', $retailerId)
            ->whereNull('retail_store_id')
            ->where('status', 'delivered')
            ->sum('total_amount');

        // Avg. Transaction Value
        $avgTransactionValue = Order::where('retailer_id', $retailerId)
            ->where('status', 'delivered')
            ->avg('total_amount');

        return response()->json([
            'total_sales' => [
                'daily' => $dailySales,
                'weekly' => $weeklySales,
            ],
            'top_selling_skus' => $topSellingSkus,
            'sales_by_channel' => [
                'in_store' => $inStoreSales,
                'online' => $onlineSales,
            ],
            'avg_transaction_value' => $avgTransactionValue,
        ]);
    }

    // 2. Inventory Intelligence Panel
    public function inventoryIntelligence(Request $request)
    {
        $retailerId = $request->user()->id;
        // Stock Levels per Location
        $stockLevels = Inventory::whereHas('product', function($q) use ($retailerId) {
                $q->where('manufacturer_id', $retailerId)->orWhere('vendor_id', $retailerId);
            })
            ->with(['retailStore:id,name', 'product:id,sku,name'])
            ->get()
            ->groupBy('retail_store_id')
            ->map(function($items, $storeId) {
                return [
                    'store_id' => $storeId,
                    'store_name' => optional($items->first()->retailStore)->name,
                    'products' => $items->map(function($inv) {
                        return [
                            'sku' => $inv->product->sku ?? null,
                            'name' => $inv->product->name ?? null,
                            'quantity' => $inv->quantity,
                        ];
                    }),
                ];
            })->values();

        // Inventory Turnover Ratio (approximate: sales/avg inventory)
        $totalSales = OrderItem::whereHas('order', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            })->sum('quantity');
        $avgInventory = Inventory::whereHas('product', function($q) use ($retailerId) {
                $q->where('manufacturer_id', $retailerId)->orWhere('vendor_id', $retailerId);
            })->avg('quantity');
        $turnoverRatio = $avgInventory ? round($totalSales / $avgInventory, 2) : 0;

        // Aging Stock Report
        $agingStock = Inventory::whereHas('product', function($q) use ($retailerId) {
                $q->where('manufacturer_id', $retailerId)->orWhere('vendor_id', $retailerId);
            })
            ->whereNotNull('last_restocked_at')
            ->with(['product:id,sku,name'])
            ->get()
            ->filter(function($inv) {
                return Carbon::parse($inv->last_restocked_at)->diffInDays(now()) > 90;
            })
            ->map(function($inv) {
                return [
                    'sku' => $inv->product->sku ?? null,
                    'name' => $inv->product->name ?? null,
                    'quantity' => $inv->quantity,
                    'last_restocked_at' => $inv->last_restocked_at,
                ];
            });

        // Reorder Point Prediction (simple: below reorder_point)
        $reorderAlerts = Inventory::whereHas('product', function($q) use ($retailerId) {
                $q->where('manufacturer_id', $retailerId)->orWhere('vendor_id', $retailerId);
            })
            ->whereColumn('quantity', '<=', 'reorder_point')
            ->with(['product:id,sku,name'])
            ->get()
            ->map(function($inv) {
                return [
                    'sku' => $inv->product->sku ?? null,
                    'name' => $inv->product->name ?? null,
                    'quantity' => $inv->quantity,
                    'reorder_point' => $inv->reorder_point,
                ];
            });

        return response()->json([
            'stock_levels_per_location' => $stockLevels,
            'inventory_turnover_ratio' => $turnoverRatio,
            'aging_stock_report' => $agingStock,
            'reorder_point_prediction' => $reorderAlerts,
        ]);
    }

    // 3. Customer Behavior & Segmentation
    public function customerBehavior(Request $request)
    {
        $retailerId = $request->user()->id;
        // Customer Lifetime Value (CLV)
        $clv = User::whereHas('orders', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            })
            ->withSum(['orders as total_spent' => function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            }], 'total_amount')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->map(function($user) {
                return [
                    'customer_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'lifetime_value' => $user->total_spent,
                ];
            });

        // Purchase Frequency
        $purchaseFrequency = User::whereHas('orders', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            })
            ->withCount(['orders as purchase_count' => function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            }])
            ->orderByDesc('purchase_count')
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->map(function($user) {
                return [
                    'customer_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'purchase_count' => $user->purchase_count,
                ];
            });

        // Product Preferences (by top category)
        $preferences = Product::select('category')
            ->whereHas('orderItems.order', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            })
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Return Rate by Segment (by customer, since no segment data)
        $returnRates = User::whereHas('orders', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId);
            })
            ->withCount(['orders as total_orders' => function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId);
            }])
            ->withCount(['orders as returned_orders' => function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'returned');
            }])
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->map(function($user) {
                $rate = $user->total_orders > 0 ? round($user->returned_orders / $user->total_orders, 2) : 0;
                return [
                    'customer_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'return_rate' => $rate,
                ];
            });

        return response()->json([
            'customer_lifetime_value' => $clv,
            'purchase_frequency' => $purchaseFrequency,
            'product_preferences' => $preferences,
            'return_rate_by_segment' => $returnRates,
        ]);
    }

    // 4. Pricing & Promotion Effectiveness
    public function pricingPromotion(Request $request)
    {
        $retailerId = $request->user()->id;
        // Markdown % Impact on Sales
        $markdowns = OrderItem::whereHas('order', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            })
            ->with('product:id,price')
            ->get()
            ->map(function($item) {
                $orig = $item->product->price ?? 0;
                $markdown = $orig > 0 ? round((($orig - $item->unit_price) / $orig) * 100, 2) : 0;
                return [
                    'product_id' => $item->product_id,
                    'sku' => $item->product->sku ?? null,
                    'original_price' => $orig,
                    'sold_price' => $item->unit_price,
                    'markdown_percent' => $markdown,
                ];
            })->filter(fn($x) => $x['markdown_percent'] > 0)->values();

        // Campaign ROI, Elasticity, Seasonal Performance: Not enough data, return placeholders
        return response()->json([
            'markdown_impact_on_sales' => $markdowns,
            'campaign_roi' => 'Not available (no campaign data)',
            'elasticity_analysis' => 'Not available (no price history)',
            'seasonal_performance' => 'Not available (no seasonality data)',
        ]);
    }

    // 5. Omnichannel Engagement Panel
    public function omnichannelEngagement(Request $request)
    {
        $retailerId = $request->user()->id;
        // Cart Abandonment Rate, Store Foot Traffic, Social Media Mentions: Not available
        // Return Rate (Online vs Store)
        $onlineReturns = Order::where('retailer_id', $retailerId)
            ->whereNull('retail_store_id')
            ->where('status', 'returned')
            ->count();
        $storeReturns = Order::where('retailer_id', $retailerId)
            ->whereNotNull('retail_store_id')
            ->where('status', 'returned')
            ->count();
        return response()->json([
            'cart_abandonment_rate' => 'Not available (no cart model)',
            'store_foot_traffic' => 'Not available (no tracking)',
            'social_media_mentions' => 'Not available (no tracking)',
            'return_rate_online_vs_store' => [
                'online' => $onlineReturns,
                'store' => $storeReturns,
            ],
        ]);
    }

    // 6. Actionable Alerts & Recommendations
    public function actionableAlerts(Request $request)
    {
        $retailerId = $request->user()->id;
        // Low-stock alerts
        $lowStock = Inventory::whereHas('product', function($q) use ($retailerId) {
                $q->where('manufacturer_id', $retailerId)->orWhere('vendor_id', $retailerId);
            })
            ->whereColumn('quantity', '<=', 'reorder_point')
            ->with(['product:id,sku,name'])
            ->get()
            ->map(function($inv) {
                return [
                    'sku' => $inv->product->sku ?? null,
                    'name' => $inv->product->name ?? null,
                    'quantity' => $inv->quantity,
                    'reorder_point' => $inv->reorder_point,
                ];
            });
        // Product bundling, trend, reorder: Suggest top-selling products
        $topProducts = OrderItem::whereHas('order', function($q) use ($retailerId) {
                $q->where('retailer_id', $retailerId)->where('status', 'delivered');
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product:sku,id,name')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'sku' => $item->product->sku ?? null,
                    'name' => $item->product->name ?? null,
                    'total_sold' => $item->total_sold,
                ];
            });
        return response()->json([
            'low_stock_alerts' => $lowStock,
            'product_bundling_suggestions' => $topProducts,
            'new_trend_alerts' => 'Not available (no market signal tracking)',
            'reorder_automation_triggers' => $lowStock,
        ]);
    }

    // 7. Market Trend Insights
    public function marketTrends(Request $request)
    {
        $retailerId = $request->user()->id;
        // Trending Products: Top sales in last 30 days
        $start = Carbon::now()->subDays(30);
        $trending = OrderItem::whereHas('order', function($q) use ($retailerId, $start) {
                $q->where('retailer_id', $retailerId)
                  ->where('status', 'delivered')
                  ->where('created_at', '>=', $start);
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product:sku,id,name')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'sku' => $item->product->sku ?? null,
                    'name' => $item->product->name ?? null,
                    'total_sold' => $item->total_sold,
                ];
            });
        // Market Growth Rate: Compare sales this month vs last month
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $thisMonthSales = Order::where('retailer_id', $retailerId)
            ->where('status', 'delivered')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount');
        $lastMonthSales = Order::where('retailer_id', $retailerId)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$lastMonth, $thisMonth->copy()->subDay()])
            ->sum('total_amount');
        $growthRate = $lastMonthSales > 0 ? round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 2) : null;
        // Seasonal Trends: Not available (no seasonality data)
        // Competitive Benchmarking: Not available (no competitor data)
        return response()->json([
            'trending_products' => $trending,
            'market_growth_rate' => $growthRate,
            'seasonal_trends' => 'Not available (no seasonality data)',
            'competitive_benchmarking' => 'Not available (no competitor data)',
        ]);
    }
} 