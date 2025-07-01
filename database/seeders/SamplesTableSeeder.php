<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sample;
use App\Models\Design;
use App\Models\Vendor;
use App\Models\User;

class SamplesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $design1 = Design::where('design_code', 'SPR24-FMD')->first();
        $design2 = Design::where('design_code', 'SUM24-BS')->first();
        $vendor1 = Vendor::where('email', 'vendor1@genzfashionz.com')->first();
        $vendor2 = Vendor::where('email', 'vendor2@genzfashionz.com')->first();
        $user1 = User::where('email', 'designer1@genzfashionz.com')->first();
        $user2 = User::where('email', 'designer2@genzfashionz.com')->first();
        $user3 = User::where('email', 'vendor1@genzfashionz.com')->first();

        Sample::firstOrCreate([
            'sample_code' => 'SPR24-FMD-S1',
        ], [
            'design_id' => $design1 ? $design1->id : null,
            'sample_type' => 'prototype',
            'size' => 'M',
            'color' => 'pink',
            'fabric_used' => 'cotton',
            'quantity' => 1,
            'status' => 'completed',
            'requested_by' => $user1 ? $user1->id : null,
            'assigned_to' => $user2 ? $user2->id : null,
            'request_date' => '2024-01-10',
            'due_date' => '2024-01-20',
            'completion_date' => '2024-01-18',
            'cost' => 20.00,
            'quality_score' => 9,
            'fit_notes' => 'Fits well, minor adjustment needed at waist.',
            'design_notes' => 'Pattern approved.',
            'production_notes' => 'Ready for bulk production.',
            'image_urls' => json_encode([]),
            'is_approved' => true,
            'approval_date' => '2024-01-19',
            'approved_by' => $user1 ? $user1->id : null,
            'rejection_reason' => null,
            'notes' => 'First prototype for Spring 2024.',
            'vendor_id' => $vendor1 ? $vendor1->id : null,
        ]);
        Sample::firstOrCreate([
            'sample_code' => 'SUM24-BS-S1',
        ], [
            'design_id' => $design2 ? $design2->id : null,
            'sample_type' => 'fit_sample',
            'size' => 'L',
            'color' => 'blue',
            'fabric_used' => 'linen',
            'quantity' => 1,
            'status' => 'in_production',
            'requested_by' => $user2 ? $user2->id : null,
            'assigned_to' => $user3 ? $user3->id : null,
            'request_date' => '2024-04-01',
            'due_date' => '2024-04-10',
            'completion_date' => null,
            'cost' => 10.00,
            'quality_score' => null,
            'fit_notes' => null,
            'design_notes' => null,
            'production_notes' => null,
            'image_urls' => json_encode([]),
            'is_approved' => false,
            'approval_date' => null,
            'approved_by' => null,
            'rejection_reason' => null,
            'notes' => 'Fit sample for Summer 2024.',
            'vendor_id' => $vendor2 ? $vendor2->id : null,
        ]);
    }
}
