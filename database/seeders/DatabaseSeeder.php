<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' =>Hash::make('password123')
        ]);User::factory()->create([
            'name' => 'Supplier',
            'email' => 'supplier@example.com',
            'password' =>Hash::make('supplier123'),
            'role'=>'supplier',
        ]);


    }
}
