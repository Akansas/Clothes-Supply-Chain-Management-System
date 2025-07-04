<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\ProductionOrder;

class MigrateRetailerOrdersToProductionOrders extends Command
{
    protected $signature = 'migrate:retailer-orders-to-production-orders';
    protected $description = 'Migrate existing retailer orders to production_orders table';

    public function handle()
    {
        $orders = Order::whereNotNull('manufacturer_id')
            ->whereNotNull('retailer_id')
            ->get();
        $count = 0;
        foreach ($orders as $order) {
            // Check if already migrated
            $exists = ProductionOrder::where('manufacturer_id', $order->manufacturer_id)
                ->where('retailer_id', $order->retailer_id)
                ->where('product_id', $order->product_id)
                ->where('due_date', $order->due_date)
                ->exists();
            if (!$exists) {
                ProductionOrder::create([
                    'order_number' => $order->order_number,
                    'manufacturer_id' => $order->manufacturer_id,
                    'retailer_id' => $order->retailer_id,
                    'product_id' => $order->product_id,
                    'quantity' => $order->quantity,
                    'due_date' => $order->due_date,
                    'status' => $order->status ?? 'pending',
                    'notes' => $order->notes,
                    'completed_at' => $order->completed_at ?? null,
                ]);
                $count++;
            }
        }
        $this->info("Migrated $count retailer orders to production_orders.");
    }
} 