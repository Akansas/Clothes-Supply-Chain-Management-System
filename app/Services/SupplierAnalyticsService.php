<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Delivery;
use App\Models\Material;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierAnalyticsService
{
    protected $user;
    protected $supplierId;

    public function __construct($user)
    {
        $this->user = $user;
        $this->supplierId = $user->id;
    }

    // 1. Demand Forecasting
    public function getDemandForecasting()
    {
        try {
            $ordersByMonth = $this->getOrdersByMonth();
            $ordersByCategory = $this->getOrdersByCategory();
            $ordersByRegion = $this->getOrdersByRegion();
            $predictedDemand = 'Not available (requires ML/external data)';

            return [
                'orders_by_month' => $ordersByMonth,
                'orders_by_category' => $ordersByCategory,
                'orders_by_region' => $ordersByRegion,
                'predicted_demand' => $predictedDemand,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getDemandForecasting: ' . $e->getMessage());
            return $this->getDefaultDemandForecasting();
        }
    }

    // 2. Lead Time & Order Fulfillment Tracking
    public function getLeadTimeTracking()
    {
        try {
            $orders = $this->getOrderLeadTimes();
            $delayCauses = 'Not available (requires workflow tracking)';
            
            return [
                'orders' => $orders,
                'delay_causes' => $delayCauses,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getLeadTimeTracking: ' . $e->getMessage());
            return $this->getDefaultLeadTimeTracking();
        }
    }

    // 3. Material Cost & Price Analytics
    public function getMaterialCostAnalytics()
    {
        try {
            $materialCosts = $this->getMaterialCosts();
            $marginImpact = 'Not available (requires product cost/profit data)';
            
            return [
                'material_costs' => $materialCosts,
                'margin_impact' => $marginImpact,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMaterialCostAnalytics: ' . $e->getMessage());
            return $this->getDefaultMaterialCostAnalytics();
        }
    }

    // 4. Quality Control Analysis
    public function getQualityControlAnalysis()
    {
        try {
            $defectRates = $this->getDefectRates();
            $returnFrequencies = $this->getReturnFrequencies();
            $compliance = 'Not available (requires certification data)';
            
            return [
                'defect_rates' => $defectRates,
                'return_frequencies' => $returnFrequencies,
                'compliance' => $compliance,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getQualityControlAnalysis: ' . $e->getMessage());
            return $this->getDefaultQualityControlAnalysis();
        }
    }

    // 5. Client Satisfaction & Relationship Metrics
    public function getClientSatisfaction()
    {
        try {
            $repeatOrders = $this->getRepeatOrders();
            $conversionRate = 'Not available (requires inquiry data)';
            $churnRate = 'Not available (requires client lifecycle data)';
            
            return [
                'repeat_orders' => $repeatOrders,
                'conversion_rate' => $conversionRate,
                'churn_rate' => $churnRate,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getClientSatisfaction: ' . $e->getMessage());
            return $this->getDefaultClientSatisfaction();
        }
    }

    // 6. Capacity Planning & Resource Utilization
    public function getCapacityPlanning()
    {
        try {
            $peakPeriods = 'Not available (requires production schedule data)';
            $workforceAvailability = 'Not available (requires workforce data)';
            $machineUptime = 'Not available (requires machine data)';
            
            return [
                'peak_periods' => $peakPeriods,
                'workforce_availability' => $workforceAvailability,
                'machine_uptime' => $machineUptime,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getCapacityPlanning: ' . $e->getMessage());
            return $this->getDefaultCapacityPlanning();
        }
    }

    // Helper Methods
    private function getOrdersByMonth()
    {
        return Order::where('supplier_id', $this->supplierId)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as order_count')
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();
    }

    private function getOrdersByCategory()
    {
        return Order::where('supplier_id', $this->supplierId)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->selectRaw('products.category, COUNT(*) as order_count')
            ->groupBy('products.category')
            ->get();
    }

    private function getOrdersByRegion()
    {
        return Order::where('supplier_id', $this->supplierId)
            ->selectRaw('shipping_state, COUNT(*) as order_count')
            ->groupBy('shipping_state')
            ->get();
    }

    private function getOrderLeadTimes()
    {
        return Order::where('supplier_id', $this->supplierId)
            ->select('id', 'order_number', 'created_at', 'confirmed_at', 'shipped_at', 'delivered_at', 'status')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($order) {
                $leadTime = $order->delivered_at && $order->confirmed_at
                    ? Carbon::parse($order->delivered_at)->diffInDays(Carbon::parse($order->confirmed_at))
                    : null;
                $delay = $order->status === 'delayed';
                return [
                    'order_number' => $order->order_number,
                    'lead_time_days' => $leadTime,
                    'status' => $order->status,
                    'delay' => $delay,
                ];
            });
    }

    private function getMaterialCosts()
    {
        return Material::where('supplier_id', $this->supplierId)
            ->select('name', 'type', 'region', 'cost', 'vendor_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getDefectRates()
    {
        return Delivery::where('supplier_id', $this->supplierId)
            ->selectRaw('reason_for_rejection, COUNT(*) as count')
            ->whereNotNull('reason_for_rejection')
            ->groupBy('reason_for_rejection')
            ->get();
    }

    private function getReturnFrequencies()
    {
        return Order::where('supplier_id', $this->supplierId)
            ->where('status', 'returned')
            ->count();
    }

    private function getRepeatOrders()
    {
        return Order::where('supplier_id', $this->supplierId)
            ->selectRaw('user_id, COUNT(*) as order_count')
            ->groupBy('user_id')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get();
    }

    // Default return methods for error handling
    private function getDefaultDemandForecasting()
    {
        return [
            'orders_by_month' => [],
            'orders_by_category' => [],
            'orders_by_region' => [],
            'predicted_demand' => 'Not available (requires ML/external data)',
        ];
    }

    private function getDefaultLeadTimeTracking()
    {
        return [
            'orders' => [],
            'delay_causes' => 'Not available (requires workflow tracking)',
        ];
    }

    private function getDefaultMaterialCostAnalytics()
    {
        return [
            'material_costs' => [],
            'margin_impact' => 'Not available (requires product cost/profit data)',
        ];
    }

    private function getDefaultQualityControlAnalysis()
    {
        return [
            'defect_rates' => [],
            'return_frequencies' => 0,
            'compliance' => 'Not available (requires certification data)',
        ];
    }

    private function getDefaultClientSatisfaction()
    {
        return [
            'repeat_orders' => [],
            'conversion_rate' => 'Not available (requires inquiry data)',
            'churn_rate' => 'Not available (requires client lifecycle data)',
        ];
    }

    private function getDefaultCapacityPlanning()
    {
        return [
            'peak_periods' => 'Not available (requires production schedule data)',
            'workforce_availability' => 'Not available (requires workforce data)',
            'machine_uptime' => 'Not available (requires machine data)',
        ];
    }
} 