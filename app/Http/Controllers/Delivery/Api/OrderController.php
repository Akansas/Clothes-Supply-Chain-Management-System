<?php

namespace App\Http\Controllers\Delivery\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Events\DeliveryStatusUpdated;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'partner';

        if ($role === 'manager') {
            $orders = [
                [ 'id' => 101, 'customer_id' => 1, 'location' => 'NY', 'delivery_partner' => 'Alex', 'status' => 'pending' ],
                [ 'id' => 102, 'customer_id' => 2, 'location' => 'LA', 'delivery_partner' => 'Sam', 'status' => 'out for delivery' ],
                [ 'id' => 103, 'customer_id' => 3, 'location' => 'TX', 'delivery_partner' => 'Jamie', 'status' => 'delivered' ],
            ];
        } else {
            $orders = [
                [ 'id' => 201, 'customer_id' => 4, 'location' => 'SF', 'status' => 'pending' ],
                [ 'id' => 202, 'customer_id' => 5, 'location' => 'LA', 'status' => 'out for delivery' ],
            ];
        }

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:delivered,failed,pending,out for delivery'
        ]);
        $user = Auth::user();
        $role = $user->role->name ?? 'partner';

        $delivery = Delivery::findOrFail($id);
        if ($role !== 'manager' && $delivery->driver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $delivery->status = $request->status;
        $delivery->save();
        event(new DeliveryStatusUpdated($delivery));
        return response()->json(['success' => true, 'status' => $delivery->status]);
    }
} 