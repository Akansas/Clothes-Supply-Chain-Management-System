<?php

namespace App\Http\Controllers\Delivery\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\DeliveryFeedback;
use App\Models\Delivery;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'partner';

        if ($role === 'manager') {
            $feedback = DeliveryFeedback::with(['order', 'customer'])
                ->latest()
                ->take(50)
                ->get()
                ->map(function($f) {
                    return [
                        'rating' => $f->rating,
                        'comment' => $f->comment,
                        'order_id' => $f->order_id,
                        'customer_name' => $f->customer ? $f->customer->name : 'Unknown',
                        'date' => $f->created_at->toDateString(),
                    ];
                });
        } else {
            // Get deliveries assigned to this partner
            $deliveryIds = Delivery::where('driver_id', $user->id)->pluck('id');
            $feedback = DeliveryFeedback::with(['order', 'customer'])
                ->whereIn('delivery_id', $deliveryIds)
                ->latest()
                ->take(50)
                ->get()
                ->map(function($f) {
                    return [
                        'rating' => $f->rating,
                        'comment' => $f->comment,
                        'order_id' => $f->order_id,
                        'customer_name' => $f->customer ? $f->customer->name : 'Unknown',
                        'date' => $f->created_at->toDateString(),
                    ];
                });
        }

        return response()->json($feedback);
    }
} 