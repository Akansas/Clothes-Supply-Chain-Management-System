<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Product::create([
            'name'=>'High quality waterproof jacket hoodie',
            'brand'=>'Gen Z fashion',
            'price'=>45000,
            'image'=>'hoodie.jpg'
        ]);
    }
}
