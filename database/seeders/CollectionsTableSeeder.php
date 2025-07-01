<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Collection;
use App\Models\Vendor;
use App\Models\User;

class CollectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendor1 = Vendor::where('email', 'vendor1@genzfashionz.com')->first();
        $vendor2 = Vendor::where('email', 'vendor2@genzfashionz.com')->first();
        $designer1 = User::where('email', 'designer1@genzfashionz.com')->first();
        $designer2 = User::where('email', 'designer2@genzfashionz.com')->first();

        Collection::firstOrCreate([
            'collection_code' => 'SPR24',
        ], [
            'name' => 'Spring 2024',
            'description' => 'Bright and fresh styles for Spring 2024.',
            'season' => 'Spring',
            'year' => 2024,
            'theme' => 'Nature',
            'inspiration' => 'Blossoms and renewal',
            'target_market' => 'Young Adults',
            'launch_date' => '2024-03-01',
            'end_date' => '2024-06-01',
            'status' => 'launched',
            'budget' => 50000,
            'expected_revenue' => 150000,
            'designer_id' => $designer1 ? $designer1->id : null,
            'vendor_id' => $vendor1 ? $vendor1->id : null,
            'image_url' => null,
            'notes' => 'Flagship collection for the year.',
            'is_active' => true,
        ]);
        Collection::firstOrCreate([
            'collection_code' => 'SUM24',
        ], [
            'name' => 'Summer 2024',
            'description' => 'Cool and vibrant summer wear.',
            'season' => 'Summer',
            'year' => 2024,
            'theme' => 'Beach',
            'inspiration' => 'Ocean and sand',
            'target_market' => 'Teens',
            'launch_date' => '2024-06-15',
            'end_date' => '2024-09-01',
            'status' => 'planning',
            'budget' => 40000,
            'expected_revenue' => 120000,
            'designer_id' => $designer2 ? $designer2->id : null,
            'vendor_id' => $vendor2 ? $vendor2->id : null,
            'image_url' => null,
            'notes' => 'Focus on swimwear and casuals.',
            'is_active' => true,
        ]);
    }
}
