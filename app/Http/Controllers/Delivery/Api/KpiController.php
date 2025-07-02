<?php

namespace App\Http\Controllers\Delivery\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'partner';

        if ($role === 'manager') {
            // Dummy global KPIs
            $data = [
                'on_time_delivery_rate' => 0.97,
                'avg_delivery_time' => 2.3, // hours
                'active_deliveries' => 12,
                'delivery_exceptions' => 1,
                'cost_per_delivery' => 4.20,
                'customer_satisfaction' => 4.8,
            ];
        } else {
            // Dummy personal KPIs
            $data = [
                'on_time_delivery_rate' => 0.95,
                'avg_delivery_time' => 2.7, // hours
                'active_deliveries' => 3,
                'delivery_exceptions' => 0,
                'cost_per_delivery' => 4.10,
                'customer_satisfaction' => 4.7,
            ];
        }

        return response()->json($data);
    }
} 