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
use App\Services\MachineLearningService;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index(MachineLearningService $ml)
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
        $pendingQualityChecks = QualityCheck::whereNull('pass_fail')->count();
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

        // Admin Analytics (example structure, adapt as needed)
        $systemKpis = (object) [
            'fulfillment_rate' => $completedOrders && $totalOrders ? $completedOrders / $totalOrders : null,
            'avg_lead_time_days' => Order::avg('lead_time_days'),
            'cost_efficiency' => Order::avg('total_amount'),
            'service_level' => $completedOrders && $totalOrders ? $completedOrders / $totalOrders : null,
            'department_comparison' => [
                'suppliers' => $userCounts['raw_material_supplier'] ?? 0,
                'manufacturers' => $userCounts['manufacturer'] ?? 0,
                'warehouses' => $userCounts['warehouse_manager'] ?? 0,
                'retailers' => $userCounts['retailer'] ?? 0,
                'delivery' => $userCounts['delivery_personnel'] ?? 0,
                'customers' => $userCounts['customer'] ?? 0,
                'inspectors' => $userCounts['inspector'] ?? 0,
            ],
            'trends' => Order::selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderByRaw('MIN(created_at)')
                ->pluck('count', 'month')->toArray(),
        ];
        $userActivity = (object) [
            'login_patterns' => [], // Fill with actual login data if available
            'permission_usage' => $roleCounts,
            'audit_trails' => [], // Fill with actual audit data if available
            'anomalies' => [], // Fill with actual anomaly data if available
        ];
        $workflowPerformance = (object) [
            'order_throughput' => [], // Fill with actual throughput data if available
            'delivery_cycles' => null,
            'bottlenecks' => null,
            'exception_handling' => null,
        ];
        $compliance = (object) [
            'inspection_logs' => null,
            'quality_audits' => (object) ['pass_rate' => null, 'fail_rate' => null],
            'compliance_flags' => null,
            'regulation_adherence' => null,
            'corrective_actions' => null,
        ];
        $riskDashboard = (object) [
            'risk_indicators' => (object) ['overdue_orders' => null],
            'supplier_reliability' => null,
        ];
        $alertsSummary = (object) [
            'real_time_alerts' => (object) ['cost_spikes' => null, 'stockouts' => null],
            'executive_summaries' => (object) ['total_orders' => $totalOrders, 'total_revenue' => Order::sum('total_amount'), 'total_users' => User::count()],
        ];

        // ML integration
        $customers = Customer::with('orders')->get()->map(function($c) {
            return [
                'id' => $c->id,
                'total_spent' => $c->orders->sum('amount'),
                'order_count' => $c->orders->count(),
            ];
        })->toArray();
        $sales = Order::all()->map(function($o) {
            return [
                'date' => $o->created_at->toDateString(),
                'amount' => $o->amount,
            ];
        })->toArray();
        $segments = $ml->segmentCustomers($customers);
        $forecast = $ml->predictDemand($sales);

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
            'supplyChainFlow',
            'systemKpis',
            'userActivity',
            'workflowPerformance',
            'compliance',
            'riskDashboard',
            'alertsSummary',
            'segments',
            'forecast'
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