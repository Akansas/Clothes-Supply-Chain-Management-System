<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductionOrder;
use App\Models\Design;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;

class ProductionOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $design1 = Design::where('design_code', 'SPR24-FMD')->first();
        $design2 = Design::where('design_code', 'SUM24-BS')->first();
        $product1 = Product::where('sku', 'SPR24-FMD')->first();
        $product2 = Product::where('sku', 'SUM24-BS')->first();
        $vendor1 = Vendor::where('email', 'vendor1@genzfashionz.com')->first();
        $vendor2 = Vendor::where('email', 'vendor2@genzfashionz.com')->first();
        $user2 = User::where('email', 'designer2@genzfashionz.com')->first();
        $user3 = User::where('email', 'vendor1@genzfashionz.com')->first();
        $user1 = User::where('email', 'designer1@genzfashionz.com')->first();

        ProductionOrder::firstOrCreate([
            'order_number' => 'PO-2024-001',
        ], [
            'design_id' => $design1 ? $design1->id : null,
            'product_id' => $product1 ? $product1->id : null,
            'vendor_id' => $vendor1 ? $vendor1->id : null,
            'quantity' => 500,
            'size_breakdown' => json_encode(['S' => 100, 'M' => 200, 'L' => 150, 'XL' => 50]),
            'color_breakdown' => json_encode(['pink' => 250, 'white' => 150, 'green' => 100]),
            'fabric_requirements' => json_encode(['cotton' => '1000m', 'lining' => '500m']),
            'accessories_requirements' => json_encode(['zipper' => 500]),
            'production_line' => 'Line 1',
            'priority' => 'high',
            'status' => 'planned',
            'start_date' => '2024-02-01',
            'due_date' => '2024-03-01',
            'completion_date' => null,
            'estimated_cost' => 7500.00,
            'actual_cost' => null,
            'quality_score' => null,
            'defect_rate' => null,
            'production_notes' => 'Urgent for spring launch.',
            'quality_notes' => null,
            'assigned_to' => $user2 ? $user2->id : null,
            'supervisor_id' => $user1 ? $user1->id : null,
            'is_rush_order' => true,
            'notes' => 'First major order for Spring 2024.',
        ]);
        ProductionOrder::firstOrCreate([
            'order_number' => 'PO-2024-002',
        ], [
            'design_id' => $design2 ? $design2->id : null,
            'product_id' => $product2 ? $product2->id : null,
            'vendor_id' => $vendor2 ? $vendor2->id : null,
            'quantity' => 300,
            'size_breakdown' => json_encode(['XS' => 50, 'S' => 100, 'M' => 100, 'L' => 50]),
            'color_breakdown' => json_encode(['blue' => 200, 'yellow' => 100]),
            'fabric_requirements' => json_encode(['linen' => '450m']),
            'accessories_requirements' => json_encode([]),
            'production_line' => 'Line 2',
            'priority' => 'medium',
            'status' => 'planned',
            'start_date' => '2024-05-01',
            'due_date' => '2024-06-01',
            'completion_date' => null,
            'estimated_cost' => 2100.00,
            'actual_cost' => null,
            'quality_score' => null,
            'defect_rate' => null,
            'production_notes' => 'For summer collection.',
            'quality_notes' => null,
            'assigned_to' => $user3 ? $user3->id : null,
            'supervisor_id' => $user2 ? $user2->id : null,
            'is_rush_order' => false,
            'notes' => 'Standard order for Summer 2024.',
        ]);
    }
}
