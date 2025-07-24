<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\SupplierReportMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Inventory;
use App\Models\Order;

class SendSupplierReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send-supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily supplier report email to all suppliers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all suppliers (assuming role name is 'supplier')
        $suppliers = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->get();

        foreach ($suppliers as $supplier) {
            // Gather report data (example: inventory and pending orders)
            $inventory = Inventory::where('supplier_id', $supplier->id)->get();
            $pendingOrders = Order::where('supplier_id', $supplier->id)->where('status', 'pending')->get();

            // Send the report email
            Mail::to($supplier->email)->send(new SupplierReportMail($supplier, $inventory, $pendingOrders));
        }

        $this->info('Supplier reports sent successfully.');
    }
} 