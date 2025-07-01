<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Design;
use App\Models\Collection;
use App\Models\Vendor;
use App\Models\User;

class DesignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collection1 = Collection::where('collection_code', 'SPR24')->first();
        $collection2 = Collection::where('collection_code', 'SUM24')->first();
        $vendor1 = Vendor::where('email', 'vendor1@genzfashionz.com')->first();
        $vendor2 = Vendor::where('email', 'vendor2@genzfashionz.com')->first();
        $designer1 = User::where('email', 'designer1@genzfashionz.com')->first();
        $designer2 = User::where('email', 'designer2@genzfashionz.com')->first();

        Design::firstOrCreate([
            'design_code' => 'SPR24-FMD',
        ], [
            'name' => 'Floral Maxi Dress',
            'description' => 'A flowing floral dress perfect for spring.',
            'designer_id' => $designer1 ? $designer1->id : null,
            'collection_id' => $collection1 ? $collection1->id : null,
            'vendor_id' => $vendor1 ? $vendor1->id : null,
            'season' => 'Spring',
            'year' => 2024,
            'category' => 'Dresses',
            'subcategory' => 'Maxi',
            'target_gender' => 'Women',
            'target_age_group' => 'Young Adults',
            'style_tags' => json_encode(['floral', 'maxi', 'casual']),
            'fabric_requirements' => json_encode(['cotton' => '2m', 'lining' => '1m']),
            'color_palette' => json_encode(['pink', 'white', 'green']),
            'size_range' => json_encode(['S', 'M', 'L', 'XL']),
            'technical_specs' => json_encode(['zipper' => 'back', 'length' => 'ankle']),
            'sample_status' => 'approved',
            'production_status' => 'not_started',
            'cost_estimate' => 15.00,
            'retail_price_target' => 45.00,
            'sustainability_score' => 8,
            'image_urls' => json_encode([]),
            'design_files' => json_encode([]),
            'notes' => 'Best seller candidate.',
            'is_active' => true,
        ]);
        Design::firstOrCreate([
            'design_code' => 'SUM24-BS',
        ], [
            'name' => 'Beach Shorts',
            'description' => 'Lightweight shorts for summer.',
            'designer_id' => $designer2 ? $designer2->id : null,
            'collection_id' => $collection2 ? $collection2->id : null,
            'vendor_id' => $vendor2 ? $vendor2->id : null,
            'season' => 'Summer',
            'year' => 2024,
            'category' => 'Bottoms',
            'subcategory' => 'Shorts',
            'target_gender' => 'Unisex',
            'target_age_group' => 'Teens',
            'style_tags' => json_encode(['beach', 'casual', 'lightweight']),
            'fabric_requirements' => json_encode(['linen' => '1.5m']),
            'color_palette' => json_encode(['blue', 'yellow']),
            'size_range' => json_encode(['XS', 'S', 'M', 'L']),
            'technical_specs' => json_encode(['drawstring' => 'waist']),
            'sample_status' => 'concept',
            'production_status' => 'not_started',
            'cost_estimate' => 7.00,
            'retail_price_target' => 25.00,
            'sustainability_score' => 7,
            'image_urls' => json_encode([]),
            'design_files' => json_encode([]),
            'notes' => 'Targeting beachwear market.',
            'is_active' => true,
        ]);
    }
}
