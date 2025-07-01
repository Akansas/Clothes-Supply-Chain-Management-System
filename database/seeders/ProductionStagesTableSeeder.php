<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductionStage;
use App\Models\ProductionOrder;
use App\Models\Vendor;
use App\Models\User;

class ProductionStagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order1 = ProductionOrder::where('order_number', 'PO-2024-001')->first();
        $order2 = ProductionOrder::where('order_number', 'PO-2024-002')->first();
        $vendor1 = Vendor::where('email', 'vendor1@genzfashionz.com')->first();
        $vendor2 = Vendor::where('email', 'vendor2@genzfashionz.com')->first();
        $user1 = User::where('email', 'designer1@genzfashionz.com')->first();
        $user2 = User::where('email', 'designer2@genzfashionz.com')->first();
        $user3 = User::where('email', 'vendor1@genzfashionz.com')->first();
        $user4 = User::where('email', 'vendor2@genzfashionz.com')->first();

        ProductionStage::firstOrCreate([
            'production_order_id' => $order1 ? $order1->id : null,
            'stage_name' => 'Cutting',
        ], [
            'stage_order' => 1,
            'estimated_duration_hours' => 8.0,
            'actual_duration_hours' => 7.5,
            'start_time' => '2024-02-01 08:00:00',
            'end_time' => '2024-02-01 15:30:00',
            'status' => 'completed',
            'assigned_to' => $user2 ? $user2->id : null,
            'supervisor_id' => $user1 ? $user1->id : null,
            'quality_score' => 9,
            'defect_count' => 2,
            'notes' => 'Smooth process.',
            'is_critical_path' => true,
            'vendor_id' => $vendor1 ? $vendor1->id : null,
        ]);
        ProductionStage::firstOrCreate([
            'production_order_id' => $order1 ? $order1->id : null,
            'stage_name' => 'Sewing',
        ], [
            'stage_order' => 2,
            'estimated_duration_hours' => 16.0,
            'actual_duration_hours' => null,
            'start_time' => null,
            'end_time' => null,
            'status' => 'pending',
            'assigned_to' => $user3 ? $user3->id : null,
            'supervisor_id' => $user1 ? $user1->id : null,
            'quality_score' => null,
            'defect_count' => null,
            'notes' => null,
            'is_critical_path' => true,
            'vendor_id' => $vendor1 ? $vendor1->id : null,
        ]);
        ProductionStage::firstOrCreate([
            'production_order_id' => $order2 ? $order2->id : null,
            'stage_name' => 'Cutting',
        ], [
            'stage_order' => 1,
            'estimated_duration_hours' => 5.0,
            'actual_duration_hours' => null,
            'start_time' => null,
            'end_time' => null,
            'status' => 'pending',
            'assigned_to' => $user4 ? $user4->id : null,
            'supervisor_id' => $user2 ? $user2->id : null,
            'quality_score' => null,
            'defect_count' => null,
            'notes' => null,
            'is_critical_path' => true,
            'vendor_id' => $vendor2 ? $vendor2->id : null,
        ]);
    }
}
