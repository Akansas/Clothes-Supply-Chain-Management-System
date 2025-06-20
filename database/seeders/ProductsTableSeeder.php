<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'T-Shirt',
                'sku' => 'TSH-001',
                'stock' => 120,
                'price' => 15.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jeans',
                'sku' => 'JNS-002',
                'stock' => 15,
                'price' => 40.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jacket',
                'sku' => 'JKT-003',
                'stock' => 0,
                'price' => 60.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
