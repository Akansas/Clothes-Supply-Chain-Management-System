<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatSupportController;
use App\Http\Controllers\CustomerController;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
	Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
	Route::get('/profile/avatar/delete', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

	// Chat routes
	Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
	Route::get('/chat/{userId}', [ChatController::class, 'show'])->name('chat.show');
	Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
	Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
	Route::post('/chat/mark-as-read', [ChatController::class, 'markAsRead'])->name('chat.mark-as-read');

	// Support Chat routes
	Route::get('/support', [App\Http\Controllers\ChatSupportController::class, 'index'])->name('chat.support');
	Route::post('/support/send', [App\Http\Controllers\ChatSupportController::class, 'sendMessage'])->name('chat.support.send');
	Route::get('/support/messages', [App\Http\Controllers\ChatSupportController::class, 'getMessages'])->name('chat.support.messages');
	Route::post('/chat/support/typing', [App\Http\Controllers\ChatSupportController::class, 'updateTypingStatus'])->name('chat.support.typing.update');
	Route::get('/chat/support/typing', [App\Http\Controllers\ChatSupportController::class, 'getTypingStatus'])->name('chat.support.typing');
	Route::get('/chat/support/download/{messageId}', [App\Http\Controllers\ChatSupportController::class, 'downloadAttachment'])->name('chat.support.download');
	
	// Retailer routes
	Route::get('/retailer/dashboard', [App\Http\Controllers\RetailerController::class, 'dashboard'])->name('retailer.dashboard');
	Route::get('/retailer/inventory', [App\Http\Controllers\InventoryController::class, 'index'])->name('retailer.inventory');
	Route::resource('/retailer/products', App\Http\Controllers\ProductController::class);
	Route::resource('/retailer/orders', App\Http\Controllers\OrderController::class);
	Route::get('/retailer/analytics', [App\Http\Controllers\AnalyticsController::class, 'sales'])->name('retailer.analytics');
	Route::get('/retailer/recommendations', [App\Http\Controllers\RecommendationsController::class, 'index'])->name('retailer.recommendations');

	// Notification routes
	Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
	Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
	Route::post('/notifications/{notificationId}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
	Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readall');

	Route::get('{page}', function ($page) {
        if (in_array($page, ['notifications'])) {
            abort(404);
        }
        return app(\App\Http\Controllers\PageController::class)->index($page);
    })->name('page.index');
	
	Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
});

