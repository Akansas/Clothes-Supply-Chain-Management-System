<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DesignersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'designer1@genzfashionz.com',
        ], [
            'name' => 'Designer One',
            'password' => bcrypt('DesignerPass1'),
            'role_id' => 4, // Assume 4 is designer role
        ]);
        User::firstOrCreate([
            'email' => 'designer2@genzfashionz.com',
        ], [
            'name' => 'Designer Two',
            'password' => bcrypt('DesignerPass2'),
            'role_id' => 4, // Assume 4 is designer role
        ]);
    }
}
