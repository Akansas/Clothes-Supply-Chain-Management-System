<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DemandPrediction;
use App\Models\CustomerSegment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display analytics dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get analytics overview
        $overview = [
            'total_revenue' => $this->calculateTotalRevenue(),
            'total_orders' => Order::count(),
            'average_order_value' => $this->calculateAverageOrderValue(),
            'customer_count' => User::whereHas('role', function($q) {
                $q->where('name', 'customer');
            })->count(),
        ];

        // Get demand predictions
        $demandPredictions = $this->getDemandPredictions();

        // Get customer segments
        $customerSegments = $this->getCustomerSegments();

        // Get sales trends
        $salesTrends = $this->getSalesTrends();

        // Get top products
        $topProducts = $this->getTopProducts();

        return view('analytics.dashboard', compact(
            'overview',
            'demandPredictions',
            'customerSegments',
            'salesTrends',
            'topProducts'
        ));
    }

    /**
     * Demand forecasting using ML
     */
    public function demandForecasting()
    {
        // Get historical sales data
        $historicalData = $this->getHistoricalSalesData();

        // Get current predictions
        $predictions = DemandPrediction::with('product')
            ->where('forecast_date', '>=', now())
            ->orderBy('forecast_date')
            ->get()
            ->groupBy('product_id');

        // Get prediction accuracy
        $accuracy = $this->calculatePredictionAccuracy();

        return view('analytics.demand-forecasting', compact(
            'historicalData',
            'predictions',
            'accuracy'
        ));
    }

    /**
     * Customer segmentation using ML
     */
    public function customerSegmentation()
    {
        // Get customer segments
        $segments = CustomerSegment::with('customer')
            ->latest('segmented_at')
            ->get()
            ->groupBy('segment');

        // Get segment statistics
        $segmentStats = $this->getSegmentStatistics();

        // Get personalized recommendations
        $recommendations = $this->getPersonalizedRecommendations();

        return view('analytics.customer-segmentation', compact(
            'segments',
            'segmentStats',
            'recommendations'
        ));
    }

    /**
     * Generate demand predictions (ML integration)
     */
    public function generatePredictions(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'forecast_period' => 'required|in:daily,weekly,monthly',
            'months_ahead' => 'required|integer|min:1|max:12',
        ]);

        // This would integrate with Python ML service
        $predictions = $this->callMLService('demand_forecast', [
            'product_id' => $request->product_id,
            'forecast_period' => $request->forecast_period,
            'months_ahead' => $request->months_ahead,
        ]);

        // Store predictions in database
        foreach ($predictions as $prediction) {
            DemandPrediction::create([
                'product_id' => $request->product_id,
                'forecast_qty' => $prediction['quantity'],
                'forecast_date' => $prediction['date'],
                'forecast_period' => $request->forecast_period,
                'confidence_level' => $prediction['confidence'],
                'model_parameters' => $prediction['parameters'] ?? null,
                'model_version' => 'v1.0',
                'predicted_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Predictions generated successfully',
            'predictions_count' => count($predictions)
        ]);
    }

    /**
     * Generate customer segments (ML integration)
     */
    public function generateSegments(Request $request)
    {
        $request->validate([
            'algorithm' => 'required|in:kmeans,dbscan,hierarchical',
            'num_segments' => 'required|integer|min:2|max:10',
        ]);

        // This would integrate with Python ML service
        $segments = $this->callMLService('customer_segmentation', [
            'algorithm' => $request->algorithm,
            'num_segments' => $request->num_segments,
        ]);

        // Store segments in database
        foreach ($segments as $segment) {
            CustomerSegment::updateOrCreate(
                ['customer_id' => $segment['customer_id']],
                [
                    'segment' => $segment['segment'],
                    'segment_score' => $segment['score'],
                    'segment_characteristics' => $segment['characteristics'] ?? null,
                    'recommendations' => $segment['recommendations'] ?? null,
                    'model_version' => 'v1.0',
                    'segmented_at' => now(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer segments generated successfully',
            'segments_count' => count($segments)
        ]);
    }

    /**
     * Sales analytics
     */
    public function salesAnalytics()
    {
        $analytics = [
            'sales_by_period' => $this->getSalesByPeriod(),
            'sales_by_product' => $this->getSalesByProduct(),
            'sales_by_region' => $this->getSalesByRegion(),
            'sales_by_customer_type' => $this->getSalesByCustomerType(),
        ];

        return view('analytics.sales', compact('analytics'));
    }

    /**
     * Supplier performance analytics
     */
    public function supplierAnalytics()
    {
        $analytics = [
            'delivery_performance' => $this->getDeliveryPerformance(),
            'quality_metrics' => $this->getQualityMetrics(),
            'cost_efficiency' => $this->getCostEfficiency(),
            'supplier_rankings' => $this->getSupplierRankings(),
        ];

        return view('analytics.supplier', compact('analytics'));
    }

    /**
     * Calculate total revenue
     */
    private function calculateTotalRevenue()
    {
        return Order::where('status', 'delivered')->sum('total_amount');
    }

    /**
     * Calculate average order value
     */
    private function calculateAverageOrderValue()
    {
        return Order::where('status', 'delivered')->avg('total_amount') ?? 0;
    }

    /**
     * Get demand predictions
     */
    private function getDemandPredictions()
    {
        return DemandPrediction::with('product')
            ->where('forecast_date', '>=', now())
            ->where('forecast_date', '<=', now()->addMonths(3))
            ->orderBy('forecast_date')
            ->get();
    }

    /**
     * Get customer segments
     */
    private function getCustomerSegments()
    {
        return CustomerSegment::with('customer')
            ->select('segment', DB::raw('count(*) as count'))
            ->groupBy('segment')
            ->get();
    }

    /**
     * Get sales trends
     */
    private function getSalesTrends()
    {
        return Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as orders'),
            DB::raw('sum(total_amount) as revenue')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top products
     */
    private function getTopProducts()
    {
        return OrderItem::select(
            'product_id',
            DB::raw('sum(quantity) as total_quantity'),
            DB::raw('sum(total_price) as total_revenue')
        )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();
    }

    /**
     * Get historical sales data
     */
    private function getHistoricalSalesData()
    {
        return OrderItem::select(
            'product_id',
            DB::raw('DATE(orders.created_at) as date'),
            DB::raw('sum(order_items.quantity) as quantity')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subMonths(12))
            ->groupBy('product_id', 'date')
            ->orderBy('date')
            ->get()
            ->groupBy('product_id');
    }

    /**
     * Calculate prediction accuracy
     */
    private function calculatePredictionAccuracy()
    {
        // This would compare actual vs predicted values
        return [
            'overall_accuracy' => 85.5,
            'mape' => 12.3, // Mean Absolute Percentage Error
            'rmse' => 45.2, // Root Mean Square Error
        ];
    }

    /**
     * Get segment statistics
     */
    private function getSegmentStatistics()
    {
        return CustomerSegment::select(
            'segment',
            DB::raw('count(*) as count'),
            DB::raw('avg(segment_score) as avg_score')
        )
            ->groupBy('segment')
            ->get();
    }

    /**
     * Get personalized recommendations
     */
    private function getPersonalizedRecommendations()
    {
        return CustomerSegment::with('customer')
            ->whereNotNull('recommendations')
            ->latest('segmented_at')
            ->take(20)
            ->get();
    }

    /**
     * Call ML service (Python integration)
     */
    private function callMLService($endpoint, $data)
    {
        // This would make HTTP request to Python ML service
        // For now, return sample data
        return [
            [
                'quantity' => 150,
                'date' => now()->addDays(30)->format('Y-m-d'),
                'confidence' => 0.85,
                'parameters' => ['model' => 'lstm', 'epochs' => 100]
            ]
        ];
    }

    /**
     * Get sales by period
     */
    private function getSalesByPeriod()
    {
        return Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('sum(total_amount) as revenue'),
            DB::raw('count(*) as orders')
        )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get sales by product
     */
    private function getSalesByProduct()
    {
        return OrderItem::select(
            'product_id',
            DB::raw('sum(quantity) as quantity'),
            DB::raw('sum(total_price) as revenue')
        )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Get sales by region
     */
    private function getSalesByRegion()
    {
        return Order::select(
            'shipping_state',
            DB::raw('sum(total_amount) as revenue'),
            DB::raw('count(*) as orders')
        )
            ->groupBy('shipping_state')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Get sales by customer type
     */
    private function getSalesByCustomerType()
    {
        return Order::join('users', 'orders.user_id', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'roles.name as customer_type',
                DB::raw('sum(orders.total_amount) as revenue'),
                DB::raw('count(orders.id) as orders')
            )
            ->groupBy('roles.name')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Get delivery performance
     */
    private function getDeliveryPerformance()
    {
        // This would calculate delivery metrics
        return [
            'on_time_delivery' => 92.5,
            'average_delivery_time' => 3.2,
            'delivery_success_rate' => 98.1,
        ];
    }

    /**
     * Get quality metrics
     */
    private function getQualityMetrics()
    {
        // This would calculate quality metrics
        return [
            'defect_rate' => 1.2,
            'return_rate' => 2.5,
            'customer_satisfaction' => 4.6,
        ];
    }

    /**
     * Get cost efficiency
     */
    private function getCostEfficiency()
    {
        // This would calculate cost metrics
        return [
            'cost_per_order' => 15.30,
            'inventory_turnover' => 4.5,
            'warehouse_utilization' => 78.2,
        ];
    }

    /**
     * Get supplier rankings
     */
    private function getSupplierRankings()
    {
        // This would rank suppliers by performance
        return [
            ['name' => 'Supplier A', 'score' => 95.2],
            ['name' => 'Supplier B', 'score' => 88.7],
            ['name' => 'Supplier C', 'score' => 82.1],
        ];
    }
}
