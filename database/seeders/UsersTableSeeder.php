<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::firstOrCreate([
            'email' => 'admin@lightbp.com',
        ], [
            'name' => 'Admin Admin',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
        ]);
    }
}
