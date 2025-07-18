<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Mail;
use App\Mail\ManufacturerReportMail;

class SendManufacturerReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-manufacturer-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily production reports to manufacturers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      \Log::info('🏭 Starting manufacturer report process...');

        $manufacturers = Manufacturer::with('productionOrders')->get();

        foreach ($manufacturers as $manufacturer) {
            $orders = $manufacturer->productionOrders;

            if ($orders->count() > 0) {
                Mail::to($manufacturer->email)->send(new ManufacturerReportMail($orders));
                \Log::info("✅ Sent report to: {$manufacturer->email}");
            } else {
                \Log::info("No production orders for: {$manufacturer->email}");
            }
        }

        $this->info('Manufacturer reports sent.');
 }
}
    

