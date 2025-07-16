<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Warehouse;

class FixProductInventory extends Command
{
    protected $signature = 'fix:product-inventory';
    protected $description = 'Ensure every product has an inventory record with a default warehouse.';

    public function handle()
    {
        $warehouse = Warehouse::first();
        if (!$warehouse) {
            $warehouse = Warehouse::create([
                'name' => 'Default Warehouse',
                'location' => 'Default Location',
                'capacity' => 1000,
                'contact_person' => 'Admin',
                'phone' => '000-000-0000',
                'email' => 'warehouse@example.com',
                'status' => 'active',
            ]);
            $this->info('Created default warehouse.');
        }
        $count = 0;
        foreach (Product::all() as $product) {
            $hasInventory = Inventory::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->exists();
            if (!$hasInventory) {
                Inventory::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'location_type' => 'warehouse',
                    'location_id' => $warehouse->id,
                    'quantity' => $product->stock_quantity ?? 0,
                    'reserved_quantity' => 0,
                    'available_quantity' => $product->stock_quantity ?? 0,
                    'status' => 'active',
                ]);
                $count++;
            }
        }
        $this->info("Added inventory records for $count products.");
    }
} 