<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\ProductionStage;
use App\Models\WorkforceAllocation;
use App\Models\QualityCheck;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManufacturerAnalyticsService
{
    protected $user;
    protected $manufacturerId;

    public function __construct($user)
    {
        $this->user = $user;
        $this->manufacturerId = $user->id;
    }

    // 1. Production Scheduling & Capacity Planning
    public function getProductionScheduling()
    {
        try {
            $upcomingOrders = $this->getUpcomingOrders();
            $activeWorkforce = $this->getActiveWorkforce();
            $machineAvailability = 'Not available (requires machine data)';
            $demandForecast = 'Not available (requires retailer/vendor forecast integration)';
            
            return [
                'upcoming_orders' => $upcomingOrders,
                'active_workforce' => $activeWorkforce,
                'machine_availability' => $machineAvailability,
                'demand_forecast' => $demandForecast,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getProductionScheduling: ' . $e->getMessage());
            return $this->getDefaultProductionScheduling();
        }
    }

    // 2. Material Consumption & Waste Tracking
    public function getMaterialConsumption()
    {
        try {
            $materialUsage = $this->getMaterialUsage();
            $supplierPerformance = 'Not available (requires supplier linkage)';
            
            return [
                'material_usage' => $materialUsage,
                'supplier_performance' => $supplierPerformance,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMaterialConsumption: ' . $e->getMessage());
            return $this->getDefaultMaterialConsumption();
        }
    }

    // 3. Order Fulfillment & Cycle Time Analysis
    public function getOrderFulfillment()
    {
        try {
            $orders = $this->getOrderCycleTimes();
            $phaseTimes = 'Not available (requires detailed stage timestamps)';
            
            return [
                'orders' => $orders,
                'phase_times' => $phaseTimes,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getOrderFulfillment: ' . $e->getMessage());
            return $this->getDefaultOrderFulfillment();
        }
    }

    // 4. Labor Efficiency & Cost Analytics
    public function getLaborEfficiency()
    {
        try {
            $outputPerOperator = $this->getOutputPerOperator();
            $overtimePatterns = 'Not available (requires attendance data)';
            
            return [
                'output_per_operator' => $outputPerOperator,
                'overtime_patterns' => $overtimePatterns,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getLaborEfficiency: ' . $e->getMessage());
            return $this->getDefaultLaborEfficiency();
        }
    }

    // 5. Quality Control & Defect Rate Analysis
    public function getQualityControl()
    {
        try {
            $defectRates = $this->getDefectRates();
            $returnsAndRework = 'Not available (requires returns/rework linkage)';
            $benchmarking = 'Not available (requires multi-factory data)';
            
            return [
                'defect_rates' => $defectRates,
                'returns_and_rework' => $returnsAndRework,
                'benchmarking' => $benchmarking,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getQualityControl: ' . $e->getMessage());
            return $this->getDefaultQualityControl();
        }
    }

    // 6. Cost Optimization & Profitability Analysis
    public function getCostOptimization()
    {
        try {
            $costs = $this->getCostBreakdown();
            $marginAnalysis = 'Not available (requires sales/profit data)';
            
            return [
                'costs' => $costs,
                'margin_analysis' => $marginAnalysis,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getCostOptimization: ' . $e->getMessage());
            return $this->getDefaultCostOptimization();
        }
    }

    // 7. Workflow Automation & Alert Systems
    public function getWorkflowAlerts()
    {
        try {
            $alerts = [
                'resource_usage' => 'Not available (requires real-time monitoring)',
                'defect_rate' => 'Not available (requires real-time monitoring)',
                'production_targets' => 'Not available (requires real-time monitoring)',
                'material_shortages' => 'Not available (requires inventory triggers)',
            ];
            return $alerts;
        } catch (\Exception $e) {
            Log::error('Error in getWorkflowAlerts: ' . $e->getMessage());
            return $this->getDefaultWorkflowAlerts();
        }
    }

    // Helper Methods
    private function getUpcomingOrders()
    {
        return ProductionOrder::where('manufacturer_id', $this->manufacturerId)
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->get();
    }

    private function getActiveWorkforce()
    {
        return WorkforceAllocation::where('manufacturer_id', $this->manufacturerId)
            ->where('date', '>=', now()->startOfDay())
            ->get();
    }

    private function getMaterialUsage()
    {
        return Inventory::where('manufacturer_id', $this->manufacturerId)
            ->select('product_id', 'quantity', 'used', 'waste')
            ->with('product:id,name')
            ->get();
    }

    private function getOrderCycleTimes()
    {
        return ProductionOrder::where('manufacturer_id', $this->manufacturerId)
            ->with(['product:id,name', 'stages'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($order) {
                $cycleTime = $order->completed_at && $order->created_at
                    ? Carbon::parse($order->completed_at)->diffInDays(Carbon::parse($order->created_at))
                    : null;
                return [
                    'order_id' => $order->id,
                    'product' => $order->product->name ?? 'Unknown',
                    'status' => $order->status,
                    'cycle_time_days' => $cycleTime,
                ];
            });
    }

    private function getOutputPerOperator()
    {
        return WorkforceAllocation::where('manufacturer_id', $this->manufacturerId)
            ->select('operator_id', DB::raw('SUM(output) as total_output'), DB::raw('SUM(hours_worked) as total_hours'))
            ->groupBy('operator_id')
            ->with('operator:id,name')
            ->get();
    }

    private function getDefectRates()
    {
        return QualityCheck::where('manufacturer_id', $this->manufacturerId)
            ->select('defect_category', DB::raw('COUNT(*) as count'))
            ->groupBy('defect_category')
            ->get();
    }

    private function getCostBreakdown()
    {
        return ProductionOrder::where('manufacturer_id', $this->manufacturerId)
            ->select('product_id', 'labor_cost', 'material_cost', 'equipment_cost', 'overhead_cost', 'total_cost', 'quantity')
            ->with('product:id,name')
            ->get();
    }

    // Default return methods for error handling
    private function getDefaultProductionScheduling()
    {
        return [
            'upcoming_orders' => [],
            'active_workforce' => [],
            'machine_availability' => 'Not available (requires machine data)',
            'demand_forecast' => 'Not available (requires retailer/vendor forecast integration)',
        ];
    }

    private function getDefaultMaterialConsumption()
    {
        return [
            'material_usage' => [],
            'supplier_performance' => 'Not available (requires supplier linkage)',
        ];
    }

    private function getDefaultOrderFulfillment()
    {
        return [
            'orders' => [],
            'phase_times' => 'Not available (requires detailed stage timestamps)',
        ];
    }

    private function getDefaultLaborEfficiency()
    {
        return [
            'output_per_operator' => [],
            'overtime_patterns' => 'Not available (requires attendance data)',
        ];
    }

    private function getDefaultQualityControl()
    {
        return [
            'defect_rates' => [],
            'returns_and_rework' => 'Not available (requires returns/rework linkage)',
            'benchmarking' => 'Not available (requires multi-factory data)',
        ];
    }

    private function getDefaultCostOptimization()
    {
        return [
            'costs' => [],
            'margin_analysis' => 'Not available (requires sales/profit data)',
        ];
    }

    private function getDefaultWorkflowAlerts()
    {
        return [
            'resource_usage' => 'Not available (requires real-time monitoring)',
            'defect_rate' => 'Not available (requires real-time monitoring)',
            'production_targets' => 'Not available (requires real-time monitoring)',
            'material_shortages' => 'Not available (requires inventory triggers)',
        ];
    }
} 