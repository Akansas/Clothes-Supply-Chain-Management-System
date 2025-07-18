<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RawMaterialSupplier;
use App\Mail\SupplierReportMail;
use Illuminate\Support\Facades\Mail;

class SendSupplierReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-supplier-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily purchase order reports to raw material suppliers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('📦 Starting supplier report process...');

        $suppliers = RawMaterialSupplier::with('purchaseOrders')->get();

        if ($suppliers->isEmpty()) {
            \Log::info('❗ No suppliers found.');
            $this->info('No suppliers found.');
            return;
        }

        $reportsSent = 0;

        foreach ($suppliers as $supplier) {
            $todayOrders = $supplier->purchaseOrders;

            if ($todayOrders->count() > 0) {
                \Log::info('📧 Sending email to: ' . $supplier->email);
                Mail::to($supplier->email)->send(new SupplierReportMail($todayOrders));
                $reportsSent++;
            } else {
                \Log::info('⛔ No purchase orders for supplier: ' . $supplier->email);
            }
        }

        if ($reportsSent > 0) {
            $this->info("✅ Supplier reports sent successfully to {$reportsSent} supplier(s).");
        } else {
            $this->info('❌ No supplier reports to send today.');
        }

        \Log::info('📤 Supplier report process finished.');
  }
}
