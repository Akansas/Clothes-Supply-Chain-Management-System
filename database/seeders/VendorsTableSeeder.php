<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\User;

class VendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendorUser1 = User::firstOrCreate([
            'email' => 'vendor1@genzfashionz.com',
        ], [
            'name' => 'Vendor One',
            'password' => bcrypt('VendorPass1'),
            'role_id' => 3, // Assume 3 is vendor role
        ]);
        $vendorUser2 = User::firstOrCreate([
            'email' => 'vendor2@genzfashionz.com',
        ], [
            'name' => 'Vendor Two',
            'password' => bcrypt('VendorPass2'),
            'role_id' => 3, // Assume 3 is vendor role
        ]);
        Vendor::firstOrCreate([
            'user_id' => $vendorUser1->id,
        ], [
            'name' => 'Vendor One',
            'contact_person' => 'Alice Vendor',
            'email' => 'vendor1@genzfashionz.com',
            'phone' => '111-222-3333',
            'address' => '1 Vendor Lane',
            'business_type' => 'Manufacturer',
            'status' => 'approved',
            'financial_stability_score' => 8.5,
            'reputation_score' => 9.0,
            'compliance_score' => 9.5,
            'notes' => 'Preferred vendor.',
        ]);
        Vendor::firstOrCreate([
            'user_id' => $vendorUser2->id,
        ], [
            'name' => 'Vendor Two',
            'contact_person' => 'Bob Vendor',
            'email' => 'vendor2@genzfashionz.com',
            'phone' => '444-555-6666',
            'address' => '2 Vendor Lane',
            'business_type' => 'Retailer',
            'status' => 'approved',
            'financial_stability_score' => 8.0,
            'reputation_score' => 8.5,
            'compliance_score' => 9.0,
            'notes' => 'Reliable partner.',
        ]);
    }
}
