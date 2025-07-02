<?php

namespace App\Http\Controllers\Delivery\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WorkforceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'partner';
        if ($role === 'manager') {
            $partners = DeliveryPartner::with('user')->get()->map(function($p) {
                return [
                    'name' => $p->user ? $p->user->name : $p->name,
                    'on_time_rate' => rand(90, 99),
                    'avg_feedback' => number_format($p->rating, 1),
                    'efficiency' => ['High','Medium','Low'][rand(0,2)],
                    'vehicle' => [
                        'fuel' => rand(30, 100) . '%',
                        'maintenance' => rand(0,1) ? 'OK' : 'Due soon',
                    ]
                ];
            });
            return response()->json($partners);
        } else {
            $partner = DeliveryPartner::where('user_id', $user->id)->first();
            if (!$partner) return response()->json([]);
            return response()->json([
                [
                    'name' => $user->name,
                    'on_time_rate' => rand(90, 99),
                    'avg_feedback' => number_format($partner->rating, 1),
                    'efficiency' => ['High','Medium','Low'][rand(0,2)],
                    'vehicle' => [
                        'fuel' => rand(30, 100) . '%',
                        'maintenance' => rand(0,1) ? 'OK' : 'Due soon',
                    ]
                ]
            ]);
        }
    }
} 