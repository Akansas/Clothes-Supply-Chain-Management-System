<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Mail;
use App\Mail\WarehouseManagerReportMail;

class SendWarehouseManagerReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-warehouse-manager-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily inventory reports to warehouse managers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         \Log::info('🏢 Starting warehouse manager report process...');

        $warehouses = Warehouse::with(['inventory.product'])->get();

        foreach ($warehouses as $warehouse) {
            $inventory = $warehouse->inventory;

            if ($inventory->count() > 0) {
                \Log::info("📧 Sending report to warehouse manager: {$warehouse->email}");
                Mail::to($warehouse->email)->send(new WarehouseManagerReportMail($inventory));
                \Log::info("✅ Report sent to: {$warehouse->email}");
            } else {
                \Log::info(" No inventory data for warehouse: {$warehouse->id}");

    }
        }
    }
}
