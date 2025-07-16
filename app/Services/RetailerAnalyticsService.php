<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RetailerAnalyticsService
{
    protected $user;
    protected $retailerId;

    public function __construct($user)
    {
        $this->user = $user;
        $this->retailerId = $user->id;
    }

    // 1. Sales Insights Panel
    public function getSalesInsights()
    {
        try {
            $today = Carbon::today();
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            // Total Sales (Daily/Weekly)
            $dailySales = $this->getSalesForPeriod($today, $today);
            $weeklySales = $this->getSalesForPeriod($weekStart, $weekEnd);

            // Top-Selling SKUs
            $topSellingSkus = $this->getTopSellingProducts(5);

            // Sales by Channel
            $salesByChannel = $this->getSalesByChannel();

            // Avg. Transaction Value
            $avgTransactionValue = $this->getAverageTransactionValue();

            return [
                'total_sales' => [
                    'daily' => $dailySales,
                    'weekly' => $weeklySales,
                ],
                'top_selling_skus' => $topSellingSkus,
                'sales_by_channel' => $salesByChannel,
                'avg_transaction_value' => $avgTransactionValue,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getSalesInsights: ' . $e->getMessage());
            return $this->getDefaultSalesInsights();
        }
    }

    // 2. Inventory Intelligence Panel
    public function getInventoryIntelligence()
    {
        try {
            $stockLevels = $this->getStockLevelsPerLocation();
            $turnoverRatio = $this->getInventoryTurnoverRatio();
            $agingStock = $this->getAgingStock();
            $reorderAlerts = $this->getReorderAlerts();

            return [
                'stock_levels_per_location' => $stockLevels,
                'inventory_turnover_ratio' => $turnoverRatio,
                'aging_stock_report' => $agingStock,
                'reorder_point_prediction' => $reorderAlerts,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getInventoryIntelligence: ' . $e->getMessage());
            return $this->getDefaultInventoryIntelligence();
        }
    }

    // 3. Customer Behavior & Segmentation
    public function getCustomerBehavior()
    {
        try {
            $clv = $this->getCustomerLifetimeValue();
            $purchaseFrequency = $this->getPurchaseFrequency();
            $preferences = $this->getProductPreferences();
            $returnRates = $this->getReturnRates();

            return [
                'customer_lifetime_value' => $clv,
                'purchase_frequency' => $purchaseFrequency,
                'product_preferences' => $preferences,
                'return_rate_by_segment' => $returnRates,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getCustomerBehavior: ' . $e->getMessage());
            return $this->getDefaultCustomerBehavior();
        }
    }

    // 4. Pricing & Promotion Effectiveness
    public function getPricingPromotion()
    {
        try {
            $markdowns = $this->getMarkdownImpact();
            
            return [
                'markdown_impact_on_sales' => $markdowns,
                'campaign_roi' => 'Not available (no campaign data)',
                'elasticity_analysis' => 'Not available (no price history)',
                'seasonal_performance' => 'Not available (no seasonality data)',
            ];
        } catch (\Exception $e) {
            Log::error('Error in getPricingPromotion: ' . $e->getMessage());
            return $this->getDefaultPricingPromotion();
        }
    }

    // 5. Omnichannel Engagement Panel
    public function getOmnichannelEngagement()
    {
        try {
            $returnRates = $this->getReturnRatesByChannel();
            
            return [
                'cart_abandonment_rate' => 'Not available (no cart model)',
                'store_foot_traffic' => 'Not available (no tracking)',
                'social_media_mentions' => 'Not available (no tracking)',
                'return_rate_online_vs_store' => $returnRates,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getOmnichannelEngagement: ' . $e->getMessage());
            return $this->getDefaultOmnichannelEngagement();
        }
    }

    // 6. Actionable Alerts & Recommendations
    public function getActionableAlerts()
    {
        try {
            $lowStock = $this->getLowStockAlerts();
            $topProducts = $this->getTopProductsForBundling();

            return [
                'low_stock_alerts' => $lowStock,
                'product_bundling_suggestions' => $topProducts,
                'new_trend_alerts' => 'Not available (no market signal tracking)',
                'reorder_automation_triggers' => $lowStock,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getActionableAlerts: ' . $e->getMessage());
            return $this->getDefaultActionableAlerts();
        }
    }

    // 7. Market Trend Insights
    public function getMarketTrends()
    {
        try {
            $trending = $this->getTrendingProducts();
            $growthRate = $this->getMarketGrowthRate();

            return [
                'trending_products' => $trending,
                'market_growth_rate' => $growthRate,
                'seasonal_trends' => 'Not available (no seasonality data)',
                'competitive_benchmarking' => 'Not available (no competitor data)',
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMarketTrends: ' . $e->getMessage());
            return $this->getDefaultMarketTrends();
        }
    }

    // Helper Methods
    private function getSalesForPeriod($startDate, $endDate)
    {
        return Order::where('retailer_id', $this->retailerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->sum('total_amount') ?? 0;
    }

    private function getTopSellingProducts($limit = 5)
    {
        return OrderItem::whereHas('order', function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product:sku,id,name')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'sku' => $item->product->sku ?? null,
                    'name' => $item->product->name ?? null,
                    'total_sold' => $item->total_sold,
                ];
            });
    }

    private function getSalesByChannel()
    {
        $inStoreSales = Order::where('retailer_id', $this->retailerId)
            ->whereNotNull('retail_store_id')
            ->where('status', 'delivered')
            ->sum('total_amount') ?? 0;
            
        $onlineSales = Order::where('retailer_id', $this->retailerId)
            ->whereNull('retail_store_id')
            ->where('status', 'delivered')
            ->sum('total_amount') ?? 0;

        return [
            'in_store' => $inStoreSales,
            'online' => $onlineSales,
        ];
    }

    private function getAverageTransactionValue()
    {
        return Order::where('retailer_id', $this->retailerId)
            ->where('status', 'delivered')
            ->avg('total_amount') ?? 0;
    }

    private function getStockLevelsPerLocation()
    {
        return Inventory::whereHas('product', function($q) {
                $q->where('manufacturer_id', $this->retailerId)->orWhere('vendor_id', $this->retailerId);
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
    }

    private function getInventoryTurnoverRatio()
    {
        $totalSales = OrderItem::whereHas('order', function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            })->sum('quantity') ?? 0;
            
        $avgInventory = Inventory::whereHas('product', function($q) {
                $q->where('manufacturer_id', $this->retailerId)->orWhere('vendor_id', $this->retailerId);
            })->avg('quantity') ?? 0;
            
        return $avgInventory > 0 ? round($totalSales / $avgInventory, 2) : 0;
    }

    private function getAgingStock()
    {
        return Inventory::whereHas('product', function($q) {
                $q->where('manufacturer_id', $this->retailerId)->orWhere('vendor_id', $this->retailerId);
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
    }

    private function getReorderAlerts()
    {
        return Inventory::whereHas('product', function($q) {
                $q->where('manufacturer_id', $this->retailerId)->orWhere('vendor_id', $this->retailerId);
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
    }

    private function getCustomerLifetimeValue()
    {
        return User::whereHas('orders', function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            })
            ->withSum(['orders as total_spent' => function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            }], 'total_amount')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->map(function($user) {
                return [
                    'customer_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'lifetime_value' => $user->total_spent ?? 0,
                ];
            });
    }

    private function getPurchaseFrequency()
    {
        return User::whereHas('orders', function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            })
            ->withCount(['orders as purchase_count' => function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            }])
            ->orderByDesc('purchase_count')
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->map(function($user) {
                return [
                    'customer_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'purchase_count' => $user->purchase_count ?? 0,
                ];
            });
    }

    private function getProductPreferences()
    {
        return Product::select('category')
            ->whereHas('orderItems.order', function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            })
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getReturnRates()
    {
        return User::whereHas('orders', function($q) {
                $q->where('retailer_id', $this->retailerId);
            })
            ->withCount(['orders as total_orders' => function($q) {
                $q->where('retailer_id', $this->retailerId);
            }])
            ->withCount(['orders as returned_orders' => function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'returned');
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
    }

    private function getMarkdownImpact()
    {
        return OrderItem::whereHas('order', function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
            })
            ->with('product:id,price,sku')
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
    }

    private function getReturnRatesByChannel()
    {
        $onlineReturns = Order::where('retailer_id', $this->retailerId)
            ->whereNull('retail_store_id')
            ->where('status', 'returned')
            ->count();
            
        $storeReturns = Order::where('retailer_id', $this->retailerId)
            ->whereNotNull('retail_store_id')
            ->where('status', 'returned')
            ->count();
            
        return [
            'online' => $onlineReturns,
            'store' => $storeReturns,
        ];
    }

    private function getLowStockAlerts()
    {
        return Inventory::whereHas('product', function($q) {
                $q->where('manufacturer_id', $this->retailerId)->orWhere('vendor_id', $this->retailerId);
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
    }

    private function getTopProductsForBundling()
    {
        return OrderItem::whereHas('order', function($q) {
                $q->where('retailer_id', $this->retailerId)->where('status', 'delivered');
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
    }

    private function getTrendingProducts()
    {
        $start = Carbon::now()->subDays(30);
        return OrderItem::whereHas('order', function($q) use ($start) {
                $q->where('retailer_id', $this->retailerId)
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
    }

    private function getMarketGrowthRate()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        $thisMonthSales = Order::where('retailer_id', $this->retailerId)
            ->where('status', 'delivered')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount') ?? 0;
            
        $lastMonthSales = Order::where('retailer_id', $this->retailerId)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$lastMonth, $thisMonth->copy()->subDay()])
            ->sum('total_amount') ?? 0;
            
        return $lastMonthSales > 0 ? round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 2) : null;
    }

    // Default return methods for error handling
    private function getDefaultSalesInsights()
    {
        return [
            'total_sales' => ['daily' => 0, 'weekly' => 0],
            'top_selling_skus' => [],
            'sales_by_channel' => ['in_store' => 0, 'online' => 0],
            'avg_transaction_value' => 0,
        ];
    }

    private function getDefaultInventoryIntelligence()
    {
        return [
            'stock_levels_per_location' => [],
            'inventory_turnover_ratio' => 0,
            'aging_stock_report' => [],
            'reorder_point_prediction' => [],
        ];
    }

    private function getDefaultCustomerBehavior()
    {
        return [
            'customer_lifetime_value' => [],
            'purchase_frequency' => [],
            'product_preferences' => [],
            'return_rate_by_segment' => [],
        ];
    }

    private function getDefaultPricingPromotion()
    {
        return [
            'markdown_impact_on_sales' => [],
            'campaign_roi' => 'Not available (no campaign data)',
            'elasticity_analysis' => 'Not available (no price history)',
            'seasonal_performance' => 'Not available (no seasonality data)',
        ];
    }

    private function getDefaultOmnichannelEngagement()
    {
        return [
            'cart_abandonment_rate' => 'Not available (no cart model)',
            'store_foot_traffic' => 'Not available (no tracking)',
            'social_media_mentions' => 'Not available (no tracking)',
            'return_rate_online_vs_store' => ['online' => 0, 'store' => 0],
        ];
    }

    private function getDefaultActionableAlerts()
    {
        return [
            'low_stock_alerts' => [],
            'product_bundling_suggestions' => [],
            'new_trend_alerts' => 'Not available (no market signal tracking)',
            'reorder_automation_triggers' => [],
        ];
    }

    private function getDefaultMarketTrends()
    {
        return [
            'trending_products' => [],
            'market_growth_rate' => null,
            'seasonal_trends' => 'Not available (no seasonality data)',
            'competitive_benchmarking' => 'Not available (no competitor data)',
        ];
    }
} 