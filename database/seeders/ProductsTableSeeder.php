<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Vendor;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendor1 = Vendor::where('email', 'vendor1@genzfashionz.com')->first();
        $vendor2 = Vendor::where('email', 'vendor2@genzfashionz.com')->first();

        Product::firstOrCreate([
            'sku' => 'SPR24-FMD',
        ], [
            'name' => 'Floral Maxi Dress',
            'description' => 'A flowing floral dress perfect for spring.',
            'material' => 'cotton',
            'size' => 'M',
            'color' => 'pink',
            'price' => 45.00,
            'cost' => 15.00,
            'category' => 'finished_product',
            'unit' => 'piece',
            'min_stock_level' => 10,
            'max_stock_level' => 1000,
            'is_active' => true,
            'supplier_id' => null,
            'manufacturer_id' => null,
            'design_id' => null,
            'season' => 'Spring',
            'collection' => 'SPR24',
            'fabric_type' => 'cotton',
            'care_instructions' => 'Machine wash',
            'sustainability_rating' => 8,
            'lead_time_days' => 14,
            'moq' => 50,
            'weight_kg' => 0.5,
            'dimensions' => json_encode(['length' => '140cm', 'width' => '50cm']),
            'barcode' => '1234567890123',
            'image_url' => null,
            'vendor_id' => $vendor1 ? $vendor1->id : null,
        ]);
        Product::firstOrCreate([
            'sku' => 'SUM24-BS',
        ], [
            'name' => 'Beach Shorts',
            'description' => 'Lightweight shorts for summer.',
            'material' => 'linen',
            'size' => 'L',
            'color' => 'blue',
            'price' => 25.00,
            'cost' => 7.00,
            'category' => 'finished_product',
            'unit' => 'piece',
            'min_stock_level' => 10,
            'max_stock_level' => 1000,
            'is_active' => true,
            'supplier_id' => null,
            'manufacturer_id' => null,
            'design_id' => null,
            'season' => 'Summer',
            'collection' => 'SUM24',
            'fabric_type' => 'linen',
            'care_instructions' => 'Hand wash',
            'sustainability_rating' => 7,
            'lead_time_days' => 10,
            'moq' => 30,
            'weight_kg' => 0.2,
            'dimensions' => json_encode(['length' => '50cm', 'width' => '40cm']),
            'barcode' => '9876543210987',
            'image_url' => null,
            'vendor_id' => $vendor2 ? $vendor2->id : null,
        ]);
    }
}
