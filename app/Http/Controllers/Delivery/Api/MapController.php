<?php

namespace App\Http\Controllers\Delivery\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\User;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $deliveries = Delivery::with(['driver', 'order'])
            ->whereIn('status', ['pending', 'in_transit', 'out_for_delivery'])
            ->get()
            ->map(function($d) {
                return [
                    'id' => $d->id,
                    'latitude' => $d->latitude,
                    'longitude' => $d->longitude,
                    'status' => $d->status,
                    'delivery_partner' => $d->driver ? $d->driver->name : null,
                    'order_id' => $d->order_id,
                ];
            });
        return response()->json($deliveries);
    }
} 