<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RetailStore;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RetailerReportMail;

class SendRetailerReports extends Command
{
    protected $signature = 'app:send-retailer-reports';
    protected $description = 'Send daily order reports to retailers';

    public function handle()
    {
        Log::info('🛒 Starting retailer report process...');

        $retailers = RetailStore::with(['orders' => function ($query) {
            $query->whereDate('created_at', today())->with('product');
        }])->get();

        foreach ($retailers as $retailer) {
            $orders = $retailer->orders;

            if ($orders->count() > 0) {
                Mail::to($retailer->email)->send(new RetailerReportMail($orders, $retailer));
                Log::info('✅ Sent retailer report to ' . $retailer->email);
            } else {
                Log::info('🟡 No orders for ' . $retailer->email);
            }
        }

        $this->info('Retailer reports sent successfully.');
  }
}