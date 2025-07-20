<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\Inventory;
use App\Models\FacilityVisit;
use App\Models\ProductionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // Supply Chain Overview Statistics - Using exact data structure from manufacturer dashboard
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        
        // Use exact data from manufacturer report
        // Purchase Orders (from suppliers) - same logic as manufacturer dashboard
        $purchaseOrderStatuses = ['pending', 'approved', 'rejected', 'delivered', 'cancelled'];
        $purchaseOrdersStats = [];
        foreach ($purchaseOrderStatuses as $status) {
            $purchaseOrdersStats[$status] = Order::where('status', $status)->count();
        }
        $totalPurchaseOrders = array_sum($purchaseOrdersStats);
        
        // Retailer Orders (to retailers) - same logic as manufacturer dashboard
        $retailerOrderStatuses = ['pending', 'approved', 'rejected', 'delivered', 'cancelled'];
        $retailerOrdersStats = [];
        foreach ($retailerOrderStatuses as $status) {
            $retailerOrdersStats[$status] = ProductionOrder::whereNotNull('retailer_id')->where('status', $status)->count();
        }
        $totalRetailerOrders = array_sum($retailerOrdersStats);
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalDeliveries = Delivery::count();
        $pendingDeliveries = Delivery::where('status', 'pending')->count();
        $totalFacilityVisits = FacilityVisit::count();
        $scheduledVisits = FacilityVisit::where('status', 'scheduled')->count();
        $totalProductionOrders = ProductionOrder::count();
        $activeProductionOrders = ProductionOrder::whereIn('status', ['in_progress', 'pending'])->count();
        // Use the calculated total from the breakdown above
        $retailerOrders = $totalRetailerOrders;

        // Recent Activities
        $recentOrders = Order::with(['user', 'orderItems.product'])->latest()->take(5)->get();
        $recentDeliveries = Delivery::with(['order', 'driver'])->latest()->take(5)->get();
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
                'active_orders' => 0, // No pending quality checks
                'recent_activity' => 'Quality checks pending'
            ]
        ];

        // Admin Analytics (example structure, adapt as needed)
        // $systemKpis = (object) [...];
        // $userActivity = (object) [...];
        // $workflowPerformance = (object) [...];
        // $compliance = (object) [...];
        // $riskDashboard = (object) [...];
        // $alertsSummary = (object) [...];

        return view('admin.dashboard', compact(
            'roles',
            'roleCounts',
            'users',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'purchaseOrdersStats',
            'retailerOrdersStats',
            'totalPurchaseOrders',
            'totalRetailerOrders',
            'totalProducts',
            'activeProducts',
            'totalDeliveries',
            'pendingDeliveries',
            'totalFacilityVisits',
            'scheduledVisits',
            'totalProductionOrders',
            'activeProductionOrders',
            'retailerOrders',
            'recentOrders',
            'recentDeliveries',
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
        
        if ($roleName === 'vendor') {
            // For vendors, load vendor data and applications
            $users = User::where('role_id', $role->id)
                ->with(['vendor', 'vendor.applications', 'vendor.latestApplication'])
                ->get();
            
            // Debug: Log the data being loaded
            Log::info('Vendor users data:', [
                'total_users' => $users->count(),
                'users_with_vendor' => $users->filter(function($user) { return $user->vendor; })->count(),
                'users_data' => $users->map(function($user) {
                    return [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'has_vendor' => $user->vendor ? 'yes' : 'no',
                        'vendor_phone' => $user->vendor ? $user->vendor->phone : 'no vendor',
                        'user_phone' => $user->phone,
                        'latest_application' => $user->vendor && $user->vendor->latestApplication ? $user->vendor->latestApplication->status : 'no application'
                    ];
                })->toArray()
            ]);
        } elseif ($roleName === 'raw_material_supplier') {
            // For raw material suppliers, load supplier data
            $users = User::where('role_id', $role->id)
                ->with(['rawMaterialSupplier'])
                ->get();
            
            // Debug: Log the data being loaded
            Log::info('Raw material supplier users data:', [
                'total_users' => $users->count(),
                'users_with_supplier' => $users->filter(function($user) { return $user->rawMaterialSupplier; })->count(),
                'users_data' => $users->map(function($user) {
                    return [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'has_supplier' => $user->rawMaterialSupplier ? 'yes' : 'no',
                        'supplier_phone' => $user->rawMaterialSupplier ? $user->rawMaterialSupplier->phone : 'no supplier',
                        'user_phone' => $user->phone,
                    ];
                })->toArray()
            ]);
        } elseif ($roleName === 'manufacturer') {
            // For manufacturers, load manufacturer data
            $users = User::where('role_id', $role->id)
                ->with(['manufacturer'])
                ->get();
            
            // Debug: Log the data being loaded
            Log::info('Manufacturer users data:', [
                'total_users' => $users->count(),
                'users_with_manufacturer' => $users->filter(function($user) { return $user->manufacturer; })->count(),
                'users_data' => $users->map(function($user) {
                    return [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'has_manufacturer' => $user->manufacturer ? 'yes' : 'no',
                        'manufacturer_phone' => $user->manufacturer ? $user->manufacturer->phone : 'no manufacturer',
                        'user_phone' => $user->phone,
                    ];
                })->toArray()
            ]);
        } elseif ($roleName === 'retailer') {
            // For retailers, load retail store data
            $users = User::where('role_id', $role->id)
                ->with(['managedRetailStore'])
                ->get();
            
            // Debug: Log the data being loaded
            Log::info('Retailer users data:', [
                'total_users' => $users->count(),
                'users_with_store' => $users->filter(function($user) { return $user->managedRetailStore; })->count(),
                'users_data' => $users->map(function($user) {
                    return [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'has_store' => $user->managedRetailStore ? 'yes' : 'no',
                        'store_phone' => $user->managedRetailStore ? $user->managedRetailStore->phone : 'no store',
                        'user_phone' => $user->phone,
                    ];
                })->toArray()
            ]);
        } else {
            $users = User::where('role_id', $role->id)->with(['orders', 'deliveries'])->get();
        }
        
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
            $adminId = session('impersonate');
            session()->forget('impersonate');
            
            // Log back in as the admin
            Auth::loginUsingId($adminId);
            
            return redirect()->route('admin.dashboard')->with('success', 'Impersonation stopped. You are now back to your admin account.');
        }
        
        return redirect()->route('admin.dashboard')->with('error', 'No impersonation session found.');
    }
} 