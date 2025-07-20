<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\SupplierReportMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Order;

class SendAdminSampleReport extends Command
{
    protected $signature = 'reports:send-admin-sample';
    protected $description = 'Send a sample supplier report to the admin email';

    public function handle()
    {
        // Replace with your real admin email
        $adminEmail = 'admin@gmail.com'; // Change this to your real admin email

        // Use any supplier or create dummy data
        $supplier = User::where('email', $adminEmail)->first() ?? User::first();
        $inventory = Inventory::limit(3)->get();
        $pendingOrders = Order::where('status', 'pending')->limit(3)->get();

        Mail::to($adminEmail)->send(new SupplierReportMail($supplier, $inventory, $pendingOrders));

        $this->info('Sample supplier report sent to admin.');
    }
} 