<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryPartner;
use App\Models\Order;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\RetailStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show delivery dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $deliveryPartner = $user->deliveryPartner;
        
        if (!$deliveryPartner) {
            return redirect()->route('delivery.profile.create')->with('error', 'Please complete your delivery partner profile first.');
        }

        // Statistics
        $stats = [
            'total_deliveries' => Delivery::where('driver_id', $user->id)->count(),
            'pending_deliveries' => Delivery::where('driver_id', $user->id)
                ->whereIn('status', ['pending', 'in_transit'])->count(),
            'completed_deliveries' => Delivery::where('driver_id', $user->id)
                ->where('status', 'delivered')->count(),
            'today_deliveries' => Delivery::where('driver_id', $user->id)
                ->whereDate('created_at', today())->count(),
            'monthly_deliveries' => Delivery::where('driver_id', $user->id)
                ->whereMonth('created_at', now()->month)->count(),
            'on_time_deliveries' => Delivery::where('driver_id', $user->id)
                ->where('status', 'delivered')
                ->where('actual_delivery_date', '<=', 'estimated_delivery')
                ->count(),
        ];

        // Today's deliveries
        $todayDeliveries = Delivery::where('driver_id', $user->id)
            ->whereDate('created_at', today())
            ->with(['order.user', 'order.orderItems.product'])
            ->orderBy('estimated_delivery')
            ->get();

        // Pending deliveries
        $pendingDeliveries = Delivery::where('driver_id', $user->id)
            ->whereIn('status', ['pending', 'in_transit'])
            ->with(['order.user', 'order.orderItems.product'])
            ->orderBy('estimated_delivery')
            ->take(10)
            ->get();

        // Recent completed deliveries
        $recentCompleted = Delivery::where('driver_id', $user->id)
            ->where('status', 'delivered')
            ->with(['order.user'])
            ->latest('actual_delivery_date')
            ->take(5)
            ->get();

        // Delivery performance
        $performance = [
            'on_time_rate' => ($stats['total_deliveries'] > 0 && $stats['completed_deliveries'] > 0)
                ? round(($stats['on_time_deliveries'] / $stats['completed_deliveries']) * 100, 2)
                : 0,
            'avg_delivery_time' => Delivery::where('driver_id', $user->id)
                ->where('status', 'delivered')
                ->whereNotNull('actual_delivery_date')
                ->selectRaw('AVG(DATEDIFF(actual_delivery_date, created_at)) as avg_days')
                ->first()->avg_days ?? 0,
        ];

        return view('delivery.dashboard', compact('stats', 'todayDeliveries', 'pendingDeliveries', 'recentCompleted', 'performance', 'deliveryPartner'));
    }

    /**
     * Show delivery partner profile creation form
     */
    public function createProfile()
    {
        $user = auth()->user();
        
        // Check if profile already exists
        if ($user->deliveryPartner) {
            return redirect()->route('delivery.dashboard')
                ->with('info', 'Profile already exists.');
        }
        
        return view('delivery.profile.create');
    }

    /**
     * Store delivery partner profile
     */
    public function storeProfile(Request $request)
    {
        $user = auth()->user();
        
        // Check if profile already exists
        if ($user->deliveryPartner) {
            return redirect()->route('delivery.dashboard')
                ->with('info', 'Profile already exists.');
        }
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'vehicle_type' => 'required|string|max:100',
            'vehicle_number' => 'required|string|max:50',
            'license_number' => 'required|string|max:100',
            'service_areas' => 'required|array|min:1',
            'service_areas.*' => 'string|max:255',
            'availability' => 'required|in:full_time,part_time,on_demand',
            'experience_years' => 'required|integer|min:0|max:50',
        ]);
        
        // Create delivery partner profile
        $deliveryPartner = DeliveryPartner::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_number' => $request->vehicle_number,
            'license_number' => $request->license_number,
            'service_areas' => json_encode($request->service_areas),
            'availability' => $request->availability,
            'experience_years' => $request->experience_years,
            'status' => 'active',
        ]);
        
        return redirect()->route('delivery.dashboard')
            ->with('success', 'Delivery partner profile created successfully!');
    }

    /**
     * Show all deliveries
     */
    public function deliveries(Request $request)
    {
        $user = auth()->user();
        
        $query = Delivery::where('driver_id', $user->id)
            ->with(['order.user', 'order.orderItems.product']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by priority
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $deliveries = $query->latest()->paginate(15);

        return view('delivery.deliveries.index', compact('deliveries'));
    }

    /**
     * Show delivery details
     */
    public function showDelivery($id)
    {
        $user = auth()->user();
        
        $delivery = Delivery::where('driver_id', $user->id)
            ->with(['order.user', 'order.orderItems.product', 'order.retailStore'])
            ->findOrFail($id);

        return view('delivery.deliveries.show', compact('delivery'));
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus(Request $request, $id)
    {
        $user = auth()->user();
        
        $delivery = Delivery::where('driver_id', $user->id)->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,in_transit,out_for_delivery,delivered,failed',
            'notes' => 'nullable|string',
            'location' => 'nullable|string',
        ]);
        
        $delivery->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'current_location' => $request->location,
        ]);
        
        // Update timestamps based on status
        switch ($request->status) {
            case 'in_transit':
                if (!$delivery->in_transit_at) {
                    $delivery->update(['in_transit_at' => now()]);
                }
                break;
            case 'out_for_delivery':
                if (!$delivery->out_for_delivery_at) {
                    $delivery->update(['out_for_delivery_at' => now()]);
                }
                break;
            case 'delivered':
                $delivery->update([
                    'actual_delivery_date' => now(),
                    'delivered_at' => now(),
                ]);
                // Update order status
                $delivery->order->update(['status' => 'delivered']);
                break;
            case 'failed':
                $delivery->update(['failed_at' => now()]);
                break;
        }
        
        return redirect()->route('delivery.deliveries.show', $delivery->id)
            ->with('success', 'Delivery status updated successfully!');
    }

    /**
     * Show route optimization
     */
    public function routeOptimization()
    {
        $user = auth()->user();
        
        // Get pending deliveries for route optimization
        $pendingDeliveries = Delivery::where('driver_id', $user->id)
            ->whereIn('status', ['pending', 'in_transit'])
            ->with(['order.user'])
            ->orderBy('estimated_delivery')
            ->get();

        // Group by area/region for route planning
        $deliveriesByArea = $pendingDeliveries->groupBy(function ($delivery) {
            // Extract area from delivery address (simplified)
            $address = $delivery->order->shipping_address ?? '';
            return $this->extractAreaFromAddress($address);
        });

        return view('delivery.route-optimization', compact('pendingDeliveries', 'deliveriesByArea'));
    }

    /**
     * Extract area from address (simplified)
     */
    private function extractAreaFromAddress($address)
    {
        // This is a simplified area extraction
        // In a real system, you'd use geocoding services
        $areas = ['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu'];
        foreach ($areas as $area) {
            if (stripos($address, $area) !== false) {
                return $area;
            }
        }
        return 'Other';
    }

    /**
     * Show delivery schedule
     */
    public function schedule(Request $request)
    {
        $user = auth()->user();
        
        $date = $request->date ?? today();
        
        $scheduledDeliveries = Delivery::where('driver_id', $user->id)
            ->whereDate('estimated_delivery', $date)
            ->with(['order.user', 'order.orderItems.product'])
            ->orderBy('estimated_delivery')
            ->get();

        // Group by time slots
        $timeSlots = [
            'morning' => $scheduledDeliveries->filter(function ($delivery) {
                $hour = $delivery->estimated_delivery ? $delivery->estimated_delivery->hour : 0;
                return $hour >= 6 && $hour < 12;
            }),
            'afternoon' => $scheduledDeliveries->filter(function ($delivery) {
                $hour = $delivery->estimated_delivery ? $delivery->estimated_delivery->hour : 0;
                return $hour >= 12 && $hour < 18;
            }),
            'evening' => $scheduledDeliveries->filter(function ($delivery) {
                $hour = $delivery->estimated_delivery ? $delivery->estimated_delivery->hour : 0;
                return $hour >= 18 || $hour < 6;
            }),
        ];

        return view('delivery.schedule', compact('scheduledDeliveries', 'timeSlots', 'date'));
    }

    /**
     * Show delivery reports
     */
    public function reports(Request $request)
    {
        $user = auth()->user();
        
        // Monthly delivery statistics
        $monthlyStats = Delivery::where('driver_id', $user->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total, 
                        SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            ->whereYear('created_at', $request->year ?? now()->year)
            ->groupBy('month')
            ->get();

        // Delivery performance by area
        $areaPerformance = Delivery::where('driver_id', $user->id)
            ->where('status', 'delivered')
            ->join('orders', 'deliveries.order_id', '=', 'orders.id')
            ->selectRaw('SUBSTRING_INDEX(orders.shipping_address, ",", 1) as area, 
                        COUNT(*) as deliveries,
                        AVG(DATEDIFF(deliveries.actual_delivery_date, deliveries.created_at)) as avg_days')
            ->groupBy('area')
            ->orderBy('deliveries', 'desc')
            ->get();

        // On-time delivery rate
        $onTimeRate = Delivery::where('driver_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('actual_delivery_date')
            ->whereNotNull('estimated_delivery')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN actual_delivery_date <= estimated_delivery THEN 1 ELSE 0 END) as on_time
            ')
            ->first();

        $onTimePercentage = $onTimeRate->total > 0 ? 
            round(($onTimeRate->on_time / $onTimeRate->total) * 100, 2) : 0;

        return view('delivery.reports', compact('monthlyStats', 'areaPerformance', 'onTimePercentage'));
    }

    /**
     * Show delivery analytics
     */
    public function analytics()
    {
        $user = auth()->user();
        
        // Daily delivery trends (last 30 days)
        $dailyTrends = Delivery::where('driver_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as deliveries')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status distribution
        $statusDistribution = Delivery::where('driver_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Average delivery time by area
        $avgDeliveryTime = Delivery::where('driver_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('actual_delivery_date')
            ->join('orders', 'deliveries.order_id', '=', 'orders.id')
            ->selectRaw('SUBSTRING_INDEX(orders.shipping_address, ",", 1) as area, 
                        AVG(DATEDIFF(actual_delivery_date, created_at)) as avg_days')
            ->groupBy('area')
            ->get();

        // Peak delivery hours
        $peakHours = Delivery::where('driver_id', $user->id)
            ->whereNotNull('actual_delivery_date')
            ->selectRaw('HOUR(actual_delivery_date) as hour, COUNT(*) as deliveries')
            ->groupBy('hour')
            ->orderBy('deliveries', 'desc')
            ->take(5)
            ->get();

        return view('delivery.analytics', compact('dailyTrends', 'statusDistribution', 'avgDeliveryTime', 'peakHours'));
    }

    /**
     * Show profile
     */
    public function profile()
    {
        $user = auth()->user();
        $deliveryPartner = $user->deliveryPartner;
        
        return view('delivery.profile.index', compact('user', 'deliveryPartner'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $deliveryPartner = $user->deliveryPartner;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:255',
            'vehicle_number' => 'required|string|max:50',
            'service_areas' => 'nullable|string',
            'availability' => 'required|in:available,unavailable,part_time',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));
        
        if ($deliveryPartner) {
            $deliveryPartner->update([
                'vehicle_type' => $request->vehicle_type,
                'vehicle_number' => $request->vehicle_number,
                'service_areas' => $request->service_areas,
                'availability' => $request->availability,
            ]);
        }

        return redirect()->route('delivery.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show delivery map
     */
    public function deliveryMap()
    {
        $user = auth()->user();
        
        // Get today's deliveries for map display
        $todayDeliveries = Delivery::where('driver_id', $user->id)
            ->whereDate('created_at', today())
            ->with(['order.user'])
            ->get();

        // Get delivery locations (simplified coordinates)
        $deliveryLocations = $todayDeliveries->map(function ($delivery) {
            return [
                'id' => $delivery->id,
                'address' => $delivery->order->shipping_address,
                'status' => $delivery->status,
                'customer' => $delivery->order->user->name,
                'estimated_time' => $delivery->estimated_delivery,
                // In a real system, you'd have actual coordinates
                'coordinates' => $this->getCoordinatesFromAddress($delivery->order->shipping_address),
            ];
        });

        return view('delivery.map', compact('deliveryLocations'));
    }

    /**
     * Get coordinates from address (simplified)
     */
    private function getCoordinatesFromAddress($address)
    {
        // This is a simplified coordinate extraction
        // In a real system, you'd use geocoding services like Google Maps API
        $coordinates = [
            'Kampala' => [0.3476, 32.5825],
            'Entebbe' => [0.0500, 32.4600],
            'Jinja' => [0.4244, 33.2041],
            'Mbarara' => [-0.6000, 30.6500],
            'Gulu' => [2.7800, 32.3000],
        ];

        foreach ($coordinates as $city => $coords) {
            if (stripos($address, $city) !== false) {
                return $coords;
            }
        }

        return [0.3476, 32.5825]; // Default to Kampala
    }
}
