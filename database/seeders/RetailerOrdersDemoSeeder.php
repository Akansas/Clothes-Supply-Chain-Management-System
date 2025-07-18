<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionOrder;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class RetailerOrdersDemoSeeder extends Seeder
{
    public function run()
    {
        // Find a manufacturer and a retailer
        $manufacturer = User::whereHas('role', function($q) { $q->where('name', 'manufacturer'); })->first();
        $retailer = User::whereHas('role', function($q) { $q->where('name', 'retailer'); })->first();
        $product = Product::first();
        if (!$manufacturer || !$retailer || !$product) return;

        // Create 7 demo orders, one per day
        for ($i = 0; $i < 7; $i++) {
            ProductionOrder::create([
                'manufacturer_id' => $manufacturer->id,
                'retailer_id' => $retailer->id,
                'product_id' => $product->id,
                'quantity' => rand(50, 500),
                'due_date' => Carbon::now()->addDays($i+1),
                'status' => 'delivered',
                'notes' => 'DEMO_ANALYTICS', // Use this to easily delete later
                'created_at' => Carbon::now()->subDays(7 - $i),
                'updated_at' => Carbon::now()->subDays(7 - $i),
            ]);
        }
    }
} 