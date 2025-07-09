<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\Inventory;
use App\Models\QualityCheck;
use App\Models\FacilityVisit;
use App\Models\ProductionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', '!=', 'admin')->get()->unique('name');
        $userCounts = [];
        foreach ($roles as $role) {
            $userCounts[$role->name] = User::where('role_id', $role->id)->count();
        }
        $roleCounts = $userCounts;
        $users = User::with('role', 'vendor')->get();

        // Supply Chain Overview Statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalDeliveries = Delivery::count();
        $pendingDeliveries = Delivery::where('status', 'pending')->count();
        $totalQualityChecks = QualityCheck::count();
        $pendingQualityChecks = QualityCheck::whereNull('pass_fail')->count(); // Quality checks not yet completed
        $totalFacilityVisits = FacilityVisit::count();
        $scheduledVisits = FacilityVisit::where('status', 'scheduled')->count();
        $totalProductionOrders = ProductionOrder::count();
        $activeProductionOrders = ProductionOrder::whereIn('status', ['in_progress', 'pending'])->count();

        // Recent Activities
        $recentOrders = Order::with(['user', 'orderItems.product'])->latest()->take(5)->get();
        $recentDeliveries = Delivery::with(['order', 'driver'])->latest()->take(5)->get();
        $recentQualityChecks = QualityCheck::with(['inspector', 'productionOrder.product'])->latest()->take(5)->get();
        $recentFacilityVisits = FacilityVisit::with(['inspector', 'vendor'])->latest()->take(5)->get();

        // Supply Chain Flow Data
        $supplyChainFlow = [
            'suppliers' => [
                'count' => $userCounts['raw_material_supplier'] ?? 0,
                'active_orders' => Order::where('source', 'supplier')->where('status', 'pending')->count(),
                'recent_activity' => 'Material supply orders'
            ],
            'manufacturers' => [
                'count' => $userCounts['manufacturer'] ?? 0,
                'active_orders' => $activeProductionOrders,
                'recent_activity' => 'Production orders in progress'
            ],
            'warehouses' => [
                'count' => $userCounts['warehouse_manager'] ?? 0,
                'active_orders' => Inventory::where('quantity', '>', 0)->count(),
                'recent_activity' => 'Inventory management'
            ],
            'retailers' => [
                'count' => $userCounts['retailer'] ?? 0,
                'active_orders' => Order::where('source', 'retailer')->where('status', 'pending')->count(),
                'recent_activity' => 'Retail orders'
            ],
            'delivery' => [
                'count' => $userCounts['delivery_personnel'] ?? 0,
                'active_orders' => $pendingDeliveries,
                'recent_activity' => 'Pending deliveries'
            ],
            'customers' => [
                'count' => $userCounts['customer'] ?? 0,
                'active_orders' => Order::where('source', 'customer')->where('status', 'pending')->count(),
                'recent_activity' => 'Customer orders'
            ],
            'inspectors' => [
                'count' => $userCounts['inspector'] ?? 0,
                'active_orders' => $pendingQualityChecks,
                'recent_activity' => 'Quality checks pending'
            ]
        ];

        return view('admin.dashboard', compact(
            'roles', 
            'roleCounts', 
            'users',
            'totalOrders', 
            'pendingOrders', 
            'completedOrders',
            'totalProducts',
            'activeProducts',
            'totalDeliveries',
            'pendingDeliveries',
            'totalQualityChecks',
            'pendingQualityChecks',
            'totalFacilityVisits',
            'scheduledVisits',
            'totalProductionOrders',
            'activeProductionOrders',
            'recentOrders',
            'recentDeliveries',
            'recentQualityChecks',
            'recentFacilityVisits',
            'supplyChainFlow'
        ));
    }

    /**
     * Show users by role
     */
    public function usersByRole($roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $users = User::where('role_id', $role->id)->with(['orders', 'deliveries'])->get();
        
        return view('admin.users-by-role', compact('role', 'users'));
    }

    /**
     * System overview
     */
    public function systemOverview()
    {
        $stats = [
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_deliveries' => Delivery::count(),
            'total_quality_checks' => QualityCheck::count(),
            'total_facility_visits' => FacilityVisit::count(),
            'total_production_orders' => ProductionOrder::count(),
        ];

        return view('admin.system-overview', compact('stats'));
    }

    /**
     * Supply chain monitoring
     */
    public function supplyChainMonitoring()
    {
        $supplyChainData = [
            'orders_by_status' => Order::selectRaw('status, count(*) as count')->groupBy('status')->get(),
            'deliveries_by_status' => Delivery::selectRaw('status, count(*) as count')->groupBy('status')->get(),
            'quality_checks_by_status' => QualityCheck::selectRaw('pass_fail as status, count(*) as count')->groupBy('pass_fail')->get(),
            'production_orders_by_status' => ProductionOrder::selectRaw('status, count(*) as count')->groupBy('status')->get(),
        ];

        return view('admin.supply-chain-monitoring', compact('supplyChainData'));
    }

    public function impersonate(Request $request)
    {
        $adminId = Auth::id();
        $userId = $request->input('user_id');
        if (session()->has('impersonate')) {
            return redirect()->back()->with('error', 'Already impersonating a user. Please stop impersonation first.');
        }
        session(['impersonate' => $adminId]);
        Auth::loginUsingId($userId);
        session()->forget('role'); // Clear any custom session role keys
        $user = Auth::user();
        $dashboardRoute = $user->role ? $user->role->getDashboardRoute() : '/';
        return redirect($dashboardRoute)->with('success', 'You are now impersonating this user.');
    }

    public function stopImpersonate()
    {
        if (session()->has('impersonate')) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            session()->forget('impersonate');
            return redirect()->route('logout'); // Force a full logout and require login
        }
        return redirect()->route('logout');
    }
} 