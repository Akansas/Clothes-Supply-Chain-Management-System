<?php

namespace App\Http\Controllers\Delivery\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Carbon\Carbon;

class TrendsController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::now()->subDays(29)->startOfDay();
        $end = Carbon::now()->endOfDay();
        $deliveries = Delivery::whereBetween('created_at', [$start, $end])->get();
        $stats = [];
        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $dayDeliveries = $deliveries->where('created_at', '>=', $date.' 00:00:00')->where('created_at', '<=', $date.' 23:59:59');
            $total = $dayDeliveries->count();
            $onTime = $dayDeliveries->where('status', 'delivered')->count();
            $exceptions = $dayDeliveries->whereIn('status', ['failed', 'exception'])->count();
            $stats[] = [
                'date' => $date,
                'total_deliveries' => $total,
                'on_time_deliveries' => $onTime,
                'exceptions' => $exceptions,
            ];
        }
        // Dummy ML forecast for next 7 days
        $last = $stats[count($stats)-1]['total_deliveries'] ?? 10;
        $forecast = [];
        for ($i = 1; $i <= 7; $i++) {
            $forecast[] = [
                'date' => Carbon::now()->addDays($i)->toDateString(),
                'predicted_deliveries' => $last + rand(-2, 3),
            ];
        }
        return response()->json(['stats' => $stats, 'forecast' => $forecast]);
    }
} 