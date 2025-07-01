<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Hash;

class ChatDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create or find supplier
        $supplier = User::firstOrCreate(
            ['email' => 'supplier@genzfashionz.com'],
            [
                'name' => 'GenZ Supplier',
                'password' => Hash::make('password'),
                'role_id' => 8, // Adjust if needed
            ]
        );

        // Create or find manufacturer
        $manufacturer = User::firstOrCreate(
            ['email' => 'manufacturer@genzfashionz.com'],
            [
                'name' => 'GenZ Manufacturer',
                'password' => Hash::make('password'),
                'role_id' => 5, // Adjust if needed
            ]
        );

        // Create a conversation
        $conversation = Conversation::create();

        // Attach both users to the conversation
        $conversation->participants()->attach([$supplier->id, $manufacturer->id]);

        // Add a message from supplier
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $supplier->id,
            'body' => 'Hello Manufacturer, this is Supplier!'
        ]);

        // Add a message from manufacturer
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $manufacturer->id,
            'body' => 'Hello Supplier, Manufacturer here!'
        ]);
    }
} 