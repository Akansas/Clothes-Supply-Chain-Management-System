<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Vendor\ProfileController as VendorProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Custom registration routes
Route::get('/register', [CustomRegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CustomRegisterController::class, 'register']);

// Other auth routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
// POST route for logging out (required for form-based logout)
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Redirect authenticated users to their role-specific dashboard
Route::get('/home', function () {
    $user = auth()->user();
    if ($user && $user->role) {
        return redirect($user->role->getDashboardRoute());
    }
    return redirect('/dashboard');
})->name('home');

// Default dashboard for users without roles
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

// Vendor Routes
Route::prefix('vendor')->middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('vendor.dashboard');
    
    // Products Management
    Route::get('/products', [App\Http\Controllers\Vendor\DashboardController::class, 'products'])->name('vendor.products');
    Route::get('/products/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createProduct'])->name('vendor.products.create');
    Route::post('/products', [App\Http\Controllers\Vendor\DashboardController::class, 'storeProduct'])->name('vendor.products.store');
    Route::get('/products/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showProduct'])->name('vendor.products.show');
    Route::put('/products/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'updateProduct'])->name('vendor.products.update');
    
    // Applications Management
    Route::get('/applications', [App\Http\Controllers\Vendor\DashboardController::class, 'applications'])->name('vendor.applications');
    Route::get('/applications/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createApplication'])->name('vendor.applications.create');
    Route::post('/applications', [App\Http\Controllers\Vendor\DashboardController::class, 'storeApplication'])->name('vendor.applications.store');
    Route::get('/applications/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showApplication'])->name('vendor.applications.show');
    Route::put('/applications/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'updateApplication'])->name('vendor.applications.update');
    
    // Facility Visits
    Route::get('/facility-visits', [App\Http\Controllers\Vendor\DashboardController::class, 'facilityVisits'])->name('vendor.facility-visits');
    Route::get('/facility-visits/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showFacilityVisit'])->name('vendor.facility-visits.show');
    Route::put('/facility-visits/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'updateFacilityVisit'])->name('vendor.facility-visits.update');
    
    // Designs Management
    Route::get('/designs', [App\Http\Controllers\Vendor\DashboardController::class, 'designs'])->name('vendor.designs');
    Route::get('/designs/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createDesign'])->name('vendor.designs.create');
    Route::post('/designs', [App\Http\Controllers\Vendor\DashboardController::class, 'storeDesign'])->name('vendor.designs.store');
    Route::get('/designs/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showDesign'])->name('vendor.designs.show');
    
    // Samples Management
    Route::get('/samples', [App\Http\Controllers\Vendor\DashboardController::class, 'samples'])->name('vendor.samples');
    Route::get('/samples/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createSample'])->name('vendor.samples.create');
    Route::post('/samples', [App\Http\Controllers\Vendor\DashboardController::class, 'storeSample'])->name('vendor.samples.store');
    Route::get('/samples/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showSample'])->name('vendor.samples.show');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Vendor\DashboardController::class, 'analytics'])->name('vendor.analytics');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Vendor\DashboardController::class, 'profile'])->name('vendor.profile');
    Route::put('/profile', [App\Http\Controllers\Vendor\DashboardController::class, 'updateProfile'])->name('vendor.profile.update');
    // Profile creation route
    Route::get('/profile/create', [App\Http\Controllers\Vendor\ProfileController::class, 'create'])->name('vendor.profile.create');
});

// Manufacturer Routes
Route::prefix('manufacturer')->middleware(['auth', 'role:manufacturer'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Manufacturer\DashboardController::class, 'index'])->name('manufacturer.dashboard');
    
    // Production Orders
    Route::get('/production-orders', [App\Http\Controllers\Manufacturer\DashboardController::class, 'productionOrders'])->name('manufacturer.production-orders');
    Route::get('/production-orders/create', [App\Http\Controllers\Manufacturer\DashboardController::class, 'createProductionOrder'])->name('manufacturer.production-orders.create');
    Route::post('/production-orders', [App\Http\Controllers\Manufacturer\DashboardController::class, 'storeProductionOrder'])->name('manufacturer.production-orders.store');
    Route::get('/production-orders/{id}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'showProductionOrder'])->name('manufacturer.production-orders.show');
    Route::put('/production-orders/{id}/status', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateProductionOrderStatus'])->name('manufacturer.production-orders.update-status');
    
    // Production Stages
    Route::get('/production-stages', [App\Http\Controllers\Manufacturer\DashboardController::class, 'productionStages'])->name('manufacturer.production-stages');
    Route::put('/production-stages/{id}/status', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateStageStatus'])->name('manufacturer.production-stages.update-status');
    
    // Quality Checks
    Route::get('/quality-checks', [App\Http\Controllers\Manufacturer\DashboardController::class, 'qualityChecks'])->name('manufacturer.quality-checks');
    Route::get('/quality-checks/{id}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'showQualityCheck'])->name('manufacturer.quality-checks.show');
    Route::put('/quality-checks/{id}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateQualityCheck'])->name('manufacturer.quality-checks.update');
    
    // Suppliers
    Route::get('/suppliers', [App\Http\Controllers\Manufacturer\DashboardController::class, 'suppliers'])->name('manufacturer.suppliers');
    
    // Inventory
    Route::get('/inventory', [App\Http\Controllers\Manufacturer\DashboardController::class, 'inventory'])->name('manufacturer.inventory');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Manufacturer\DashboardController::class, 'analytics'])->name('manufacturer.analytics');
    
    // Raw Materials
    Route::get('/materials/browse', [App\Http\Controllers\Manufacturer\DashboardController::class, 'browseMaterials'])->name('manufacturer.materials.browse');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Manufacturer\DashboardController::class, 'profile'])->name('manufacturer.profile');
    Route::put('/profile', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateProfile'])->name('manufacturer.profile.update');

    // Raw Material Order
    Route::get('/materials/order/{material}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'orderMaterial'])->name('manufacturer.materials.order');
    Route::post('/manufacturer/materials/order/{material}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'placeMaterialOrder'])->name('manufacturer.materials.order.place');

    // Finished products (clothes) CRUD
    Route::get('/products/create', [App\Http\Controllers\Manufacturer\ProductController::class, 'create'])->name('manufacturer.products.create');
    Route::post('/products', [App\Http\Controllers\Manufacturer\ProductController::class, 'store'])->name('manufacturer.products.store');
    Route::get('/products/{id}/edit', [App\Http\Controllers\Manufacturer\ProductController::class, 'edit'])->name('manufacturer.products.edit');
    Route::put('/products/{id}', [App\Http\Controllers\Manufacturer\ProductController::class, 'update'])->name('manufacturer.products.update');
    Route::delete('/products/{id}', [App\Http\Controllers\Manufacturer\ProductController::class, 'destroy'])->name('manufacturer.products.destroy');
});

// Warehouse Routes
Route::middleware(['auth', 'role:warehouse,warehouse_manager'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Warehouse\DashboardController::class, 'index'])->name('dashboard');
    
    // Inventory Management
    Route::get('/inventory', [App\Http\Controllers\Warehouse\DashboardController::class, 'inventory'])->name('inventory');
    Route::put('/inventory/{id}', [App\Http\Controllers\Warehouse\DashboardController::class, 'updateInventory'])->name('inventory.update');
    Route::post('/inventory/add', [App\Http\Controllers\Warehouse\DashboardController::class, 'addToInventory'])->name('inventory.add');
    
    // Order Fulfillments
    Route::get('/fulfillments', [App\Http\Controllers\Warehouse\DashboardController::class, 'fulfillments'])->name('fulfillments');
    Route::get('/fulfillments/{id}', [App\Http\Controllers\Warehouse\DashboardController::class, 'showFulfillment'])->name('fulfillments.show');
    Route::post('/fulfillments/{id}/process', [App\Http\Controllers\Warehouse\DashboardController::class, 'processFulfillment'])->name('fulfillments.process');
    
    // Deliveries
    Route::get('/deliveries', [App\Http\Controllers\Warehouse\DashboardController::class, 'deliveries'])->name('deliveries');
    Route::get('/deliveries/{id}', [App\Http\Controllers\Warehouse\DashboardController::class, 'showDelivery'])->name('deliveries.show');
    Route::put('/deliveries/{id}/status', [App\Http\Controllers\Warehouse\DashboardController::class, 'updateDeliveryStatus'])->name('deliveries.update-status');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Warehouse\DashboardController::class, 'analytics'])->name('analytics');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Warehouse\DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Warehouse\DashboardController::class, 'updateProfile'])->name('profile.update');

    Route::get('assign-delivery', [\App\Http\Controllers\Warehouse\DashboardController::class, 'assignDelivery'])->name('assign-delivery');
    Route::get('chat', [\App\Http\Controllers\Warehouse\DashboardController::class, 'chat'])->name('chat');
});

// Retailer Routes
Route::prefix('retailer')->middleware(['auth', 'role:retailer'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Retailer\DashboardController::class, 'index'])->name('retailer.dashboard');
    
    // Profile routes
    Route::get('/profile/create', [App\Http\Controllers\Retailer\DashboardController::class, 'createProfile'])->name('retailer.profile.create');
    Route::post('/profile/create', [App\Http\Controllers\Retailer\DashboardController::class, 'storeProfile'])->name('retailer.profile.store');
    
    // Orders routes
    Route::get('/orders', [App\Http\Controllers\Retailer\DashboardController::class, 'orders'])->name('retailer.orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Retailer\DashboardController::class, 'showOrder'])->name('retailer.orders.show');
    Route::put('/orders/{id}/status', [App\Http\Controllers\Retailer\DashboardController::class, 'updateOrderStatus'])->name('retailer.orders.update-status');
    
    // Inventory routes
    Route::get('/inventory', [App\Http\Controllers\Retailer\DashboardController::class, 'inventory'])->name('retailer.inventory');
    Route::put('/inventory/{id}', [App\Http\Controllers\Retailer\DashboardController::class, 'updateInventory'])->name('retailer.inventory.update');
    Route::post('/inventory/add', [App\Http\Controllers\Retailer\DashboardController::class, 'addToInventory'])->name('retailer.inventory.add');
    
    // Returns routes
    Route::get('/returns', [App\Http\Controllers\Retailer\DashboardController::class, 'returns'])->name('retailer.returns');
    Route::post('/returns/{orderId}/process', [App\Http\Controllers\Retailer\DashboardController::class, 'processReturn'])->name('retailer.returns.process');
    
    // Analytics routes
    Route::get('/analytics', [App\Http\Controllers\Retailer\DashboardController::class, 'analytics'])->name('retailer.analytics');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Retailer\DashboardController::class, 'profile'])->name('retailer.profile');
    Route::put('/profile', [App\Http\Controllers\Retailer\DashboardController::class, 'updateProfile'])->name('retailer.profile.update');
});

// Delivery Routes
Route::prefix('delivery')->middleware(['auth', 'role:delivery,delivery_personnel'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Delivery\DashboardController::class, 'index'])->name('delivery.dashboard');
    
    // Profile creation routes
    Route::get('/profile/create', [App\Http\Controllers\Delivery\DashboardController::class, 'createProfile'])->name('delivery.profile.create');
    Route::post('/profile/create', [App\Http\Controllers\Delivery\DashboardController::class, 'storeProfile'])->name('delivery.profile.store');
    
    // Deliveries Management
    Route::get('/deliveries', [App\Http\Controllers\Delivery\DashboardController::class, 'deliveries'])->name('delivery.deliveries');
    Route::get('/deliveries/{id}', [App\Http\Controllers\Delivery\DashboardController::class, 'showDelivery'])->name('delivery.deliveries.show');
    Route::put('/deliveries/{id}/status', [App\Http\Controllers\Delivery\DashboardController::class, 'updateDeliveryStatus'])->name('delivery.deliveries.update-status');
    
    // Route Optimization
    Route::get('/route-optimization', [App\Http\Controllers\Delivery\DashboardController::class, 'routeOptimization'])->name('delivery.route-optimization');
    
    // Schedule
    Route::get('/schedule', [App\Http\Controllers\Delivery\DashboardController::class, 'schedule'])->name('delivery.schedule');
    
    // Reports
    Route::get('/reports', [App\Http\Controllers\Delivery\DashboardController::class, 'reports'])->name('delivery.reports');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Delivery\DashboardController::class, 'analytics'])->name('delivery.analytics');
    
    // Delivery Map
    Route::get('/map', [App\Http\Controllers\Delivery\DashboardController::class, 'deliveryMap'])->name('delivery.map');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Delivery\DashboardController::class, 'profile'])->name('delivery.profile');
    Route::put('/profile', [App\Http\Controllers\Delivery\DashboardController::class, 'updateProfile'])->name('delivery.profile.update');
});

// Customer Routes
Route::prefix('customer')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('customer.dashboard');
    
    // Products
    Route::get('/products', [App\Http\Controllers\Customer\DashboardController::class, 'browseProducts'])->name('customer.products.browse');
    Route::get('/products/{id}', [App\Http\Controllers\Customer\DashboardController::class, 'showProduct'])->name('customer.products.show');
    
    // Cart
    Route::get('/cart', [App\Http\Controllers\Customer\DashboardController::class, 'cart'])->name('customer.cart');
    Route::post('/cart/add/{productId}', [App\Http\Controllers\Customer\DashboardController::class, 'addToCart'])->name('customer.cart.add');
    Route::delete('/cart/remove/{productId}', [App\Http\Controllers\Customer\DashboardController::class, 'removeFromCart'])->name('customer.cart.remove');
    
    // Checkout
    Route::get('/checkout', [App\Http\Controllers\Customer\DashboardController::class, 'checkout'])->name('customer.checkout');
    Route::post('/checkout/place-order', [App\Http\Controllers\Customer\DashboardController::class, 'placeOrder'])->name('customer.checkout.place-order');
    
    // Orders
    Route::get('/orders', [App\Http\Controllers\Customer\DashboardController::class, 'orders'])->name('customer.orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Customer\DashboardController::class, 'showOrder'])->name('customer.orders.show');
    Route::get('/orders/{id}/track', [App\Http\Controllers\Customer\DashboardController::class, 'trackOrder'])->name('customer.orders.track');
    Route::post('/orders/{id}/cancel', [App\Http\Controllers\Customer\DashboardController::class, 'cancelOrder'])->name('customer.orders.cancel');
    
    // Profile
    Route::get('/profile', [App\Http\Controllers\Customer\DashboardController::class, 'profile'])->name('customer.profile');
    Route::put('/profile', [App\Http\Controllers\Customer\DashboardController::class, 'updateProfile'])->name('customer.profile.update');
});

// Inventory Management Routes
Route::prefix('inventory')->middleware(['auth'])->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.dashboard');
    Route::get('/analytics', [InventoryController::class, 'analytics'])->name('inventory.analytics');
    Route::get('/location/{locationType}/{locationId}', [InventoryController::class, 'showLocation'])->name('inventory.location');
    Route::post('/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
});

// Order Management Routes
Route::prefix('orders')->middleware(['auth'])->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.dashboard');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/{id}/return', [OrderController::class, 'processReturn'])->name('orders.process-return');
    Route::get('/analytics', [OrderController::class, 'analytics'])->name('orders.analytics');
});

// Chat Routes
Route::prefix('chat')->name('chat.')->middleware('auth')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
    Route::post('/{conversation}/messages', [ChatController::class, 'store'])->name('store');
    Route::get('{conversation}/messages/{message}/edit', [ChatController::class, 'editMessage'])->name('message.edit');
    Route::put('{conversation}/messages/{message}', [ChatController::class, 'updateMessage'])->name('message.update');
    Route::delete('{conversation}/messages/{message}', [ChatController::class, 'destroyMessage'])->name('message.destroy');
});

// Analytics Routes
Route::prefix('analytics')->middleware(['auth'])->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('analytics.dashboard');
    Route::get('/demand-forecasting', [AnalyticsController::class, 'demandForecasting'])->name('analytics.demand-forecasting');
    Route::get('/customer-segmentation', [AnalyticsController::class, 'customerSegmentation'])->name('analytics.customer-segmentation');
    Route::post('/generate-predictions', [AnalyticsController::class, 'generatePredictions'])->name('analytics.generate-predictions');
    Route::post('/generate-segments', [AnalyticsController::class, 'generateSegments'])->name('analytics.generate-segments');
    Route::get('/sales', [AnalyticsController::class, 'salesAnalytics'])->name('analytics.sales');
    Route::get('/supplier', [AnalyticsController::class, 'supplierAnalytics'])->name('analytics.supplier');
});

// Common authenticated routes
Route::group(['middleware' => 'auth'], function () {
    Route::resource('user', UserController::class, ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.edit', 'uses' => ProfileController::class . '@edit']);
    Route::patch('profile', ['as' => 'profile.update', 'uses' => ProfileController::class . '@update']);
    Route::patch('profile/password', ['as' => 'profile.password', 'uses' => ProfileController::class . '@password']);
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Page routes
Route::group(['middleware' => 'auth'], function () {
    Route::get('{page}', ['as' => 'page.index', 'uses' => PageController::class . '@index']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users/{roleName}', [App\Http\Controllers\Admin\DashboardController::class, 'usersByRole'])->name('admin.users-by-role');
    Route::get('/system-overview', [App\Http\Controllers\Admin\DashboardController::class, 'systemOverview'])->name('admin.system-overview');
    Route::get('/supply-chain-monitoring', [App\Http\Controllers\Admin\DashboardController::class, 'supplyChainMonitoring'])->name('admin.supply-chain-monitoring');
    Route::resource('user', UserController::class)->except(['create', 'store']);
});

// Supplier Routes
Route::prefix('supplier')->middleware(['auth', 'role:supplier,raw_material_supplier'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('supplier.dashboard');
    
    // Material Catalog Management
    Route::resource('materials', \App\Http\Controllers\Supplier\MaterialController::class, ['as' => 'supplier']);
    
    // Stock Management
    Route::get('materials/{material}/stock', [\App\Http\Controllers\Supplier\MaterialController::class, 'showStockForm'])->name('supplier.materials.stock.edit');
    Route::post('materials/{material}/stock', [\App\Http\Controllers\Supplier\MaterialController::class, 'updateStock'])->name('supplier.materials.stock.update');
    
    // Order Management
    Route::get('orders', [\App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('supplier.orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Supplier\OrderController::class, 'show'])->name('supplier.orders.show');
    Route::post('orders/{order}/status', [\App\Http\Controllers\Supplier\OrderController::class, 'updateStatus'])->name('supplier.orders.updateStatus');
    
    // Deliveries Management
    Route::get('deliveries', [\App\Http\Controllers\Supplier\DeliveryController::class, 'index'])->name('supplier.deliveries.index');
    Route::get('deliveries/{delivery}', [\App\Http\Controllers\Supplier\DeliveryController::class, 'show'])->name('supplier.deliveries.show');
    Route::post('deliveries/{delivery}/status', [\App\Http\Controllers\Supplier\DeliveryController::class, 'updateStatus'])->name('supplier.deliveries.updateStatus');
    
    // Manufacturers
    Route::get('/manufacturers', [App\Http\Controllers\Supplier\DashboardController::class, 'manufacturers'])->name('supplier.manufacturers');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Supplier\DashboardController::class, 'analytics'])->name('supplier.analytics');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Supplier\DashboardController::class, 'profile'])->name('supplier.profile');
    Route::put('/profile', [App\Http\Controllers\Supplier\DashboardController::class, 'updateProfile'])->name('supplier.profile.update');
});

// Inspector Routes
Route::prefix('inspector')->middleware(['auth', 'role:inspector'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Inspector\DashboardController::class, 'index'])->name('inspector.dashboard');
    Route::get('/quality-checks', [App\Http\Controllers\Inspector\DashboardController::class, 'qualityChecks'])->name('inspector.quality-checks');
    Route::get('/quality-checks/create', [App\Http\Controllers\Inspector\DashboardController::class, 'createQualityCheck'])->name('inspector.quality-checks.create');
    Route::post('/quality-checks', [App\Http\Controllers\Inspector\DashboardController::class, 'storeQualityCheck'])->name('inspector.quality-checks.store');
    Route::get('/quality-checks/{id}', [App\Http\Controllers\Inspector\DashboardController::class, 'showQualityCheck'])->name('inspector.quality-checks.show');
    Route::get('/quality-checks/{id}/edit', [App\Http\Controllers\Inspector\DashboardController::class, 'editQualityCheck'])->name('inspector.quality-checks.edit');
    Route::put('/quality-checks/{id}', [App\Http\Controllers\Inspector\DashboardController::class, 'updateQualityCheck'])->name('inspector.quality-checks.update');
    Route::get('/facility-visits', [App\Http\Controllers\Inspector\DashboardController::class, 'facilityVisits'])->name('inspector.facility-visits');
    Route::get('/facility-visits/create', [App\Http\Controllers\Inspector\DashboardController::class, 'createFacilityVisit'])->name('inspector.facility-visits.create');
    Route::post('/facility-visits', [App\Http\Controllers\Inspector\DashboardController::class, 'storeFacilityVisit'])->name('inspector.facility-visits.store');
    Route::get('/facility-visits/{id}', [App\Http\Controllers\Inspector\DashboardController::class, 'showFacilityVisit'])->name('inspector.facility-visits.show');
    Route::get('/facility-visits/{id}/edit', [App\Http\Controllers\Inspector\DashboardController::class, 'editFacilityVisit'])->name('inspector.facility-visits.edit');
    Route::put('/facility-visits/{id}', [App\Http\Controllers\Inspector\DashboardController::class, 'updateFacilityVisit'])->name('inspector.facility-visits.update');
    Route::get('/reports', [App\Http\Controllers\Inspector\DashboardController::class, 'reports'])->name('inspector.reports');
});

// Real-time and backend routes for all dashboards
Route::middleware('auth')->group(function () {
    Route::resource('orders', App\Http\Controllers\OrderController::class);
    Route::post('chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('chat/messages/{userId}', [App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// Product Management Routes
Route::prefix('products')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::post('/', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    Route::get('/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::get('/{id}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/{id}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::delete('/{id}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/{id}/toggle-status', [App\Http\Controllers\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::get('/{id}/analytics', [App\Http\Controllers\ProductController::class, 'analytics'])->name('products.analytics');
    Route::post('/bulk-action', [App\Http\Controllers\ProductController::class, 'bulkAction'])->name('products.bulk-action');
});

// Debug route for troubleshooting
Route::get('/debug-auth', function () {
    if (auth()->check()) {
        $user = auth()->user();
        dd([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role ? $user->role->name : 'No role',
            'session_id' => session()->getId(),
            'is_authenticated' => auth()->check(),
        ]);
    } else {
        dd('Not authenticated');
    }
})->middleware('auth');

// Supplier Order Resource Route
Route::resource('supplier/orders', App\Http\Controllers\Supplier\OrderController::class)->names('supplier.orders');

