<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use App\Models\ProductionOrder;
use App\Models\Product;
use App\Models\User;
use Phpml\Regression\LeastSquares;
use Phpml\Clustering\KMeans;

class AnalyticsController extends Controller
{
    public function index()
    {
        // --- Demand Forecasting (only MANIRAGABA BRIAN's delivered orders) ---
        $orders = \App\Models\ProductionOrder::where('retailer_id', 20)
            ->where('status', 'delivered')
            ->whereNotNull('created_at')
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $samples = [];
        $targets = [];
        foreach ($orders as $order) {
            $samples[] = [strtotime($order->date)];
            $targets[] = $order->total;
        }

        $regression = new LeastSquares();
        $predicted = null;
        if (count($samples) > 1) {
            $regression->train($samples, $targets);
            $nextDate = strtotime('+1 day', strtotime($orders->last()->date));
            $predicted = $regression->predict([$nextDate]);
        }

        // --- Retailer Segmentation (manual, all retailers) ---
        $retailers = User::where('role_id', 3)
            ->withCount(['retailerProductionOrders as delivered_orders_count' => function($q) {
                $q->where('status', 'delivered');
            }])
            ->get();

        $segments = [
            'Inactive Retailers' => [],
            'Occasional Buyers' => [],
            'High Value Retailers' => [],
        ];

        foreach ($retailers as $user) {
            $ordersCount = $user->delivered_orders_count;
            if ($ordersCount == 0) {
                $segments['Inactive Retailers'][] = [
                    'name' => $user->name,
                    'orders' => $ordersCount,
                ];
            } elseif ($ordersCount >= 1 && $ordersCount <= 5) {
                $segments['Occasional Buyers'][] = [
                    'name' => $user->name,
                    'orders' => $ordersCount,
                ];
            } else {
                $segments['High Value Retailers'][] = [
                    'name' => $user->name,
                    'orders' => $ordersCount,
                ];
            }
        }

        $segmentDescriptions = [
            'Inactive Retailers' => 'No purchases yet',
            'Occasional Buyers' => '1–5 purchases in total',
            'High Value Retailers' => '6 or more purchases',
        ];

        return view('manufacturer.analytics.index', [
            'orders' => $orders,
            'predicted' => $predicted,
            'segments' => $segments,
            'segmentDescriptions' => $segmentDescriptions,
        ]);
    }
} 