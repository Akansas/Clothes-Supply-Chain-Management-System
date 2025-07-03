<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'vendor',
                'display_name' => 'Vendor',
                'description' => 'Raw material and component suppliers',
            ],
            [
                'name' => 'manufacturer',
                'display_name' => 'Manufacturer',
                'description' => 'Product manufacturers and factories',
            ],
            [
                'name' => 'retailer',
                'display_name' => 'Retailer',
                'description' => 'Retail store owners and managers',
            ],
            [
                'name' => 'delivery_personnel',
                'display_name' => 'Delivery Personnel',
                'description' => 'Delivery drivers and logistics personnel',
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'End customers and consumers',
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'System administrators with full access',
            ],
            [
                'name' => 'raw_material_supplier',
                'display_name' => 'Supplier',
                'description' => 'Supplies fabric, thread, zippers, and other raw materials',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        DB::table('roles')->updateOrInsert([
            'name' => 'admin',
        ], [
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator',
        ]);
    }
}
