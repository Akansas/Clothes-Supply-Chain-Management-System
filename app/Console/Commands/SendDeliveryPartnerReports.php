<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeliveryPartner;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeliveryPartnerReportMail;

class SendDeliveryPartnerReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-delivery-partner-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily delivery reports to delivery partners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('📦 Starting delivery partner report process...');

        $partners = DeliveryPartner::with(['shipments' => function ($q) {
            $q->whereDate('created_at', today());
        }])->get();

        foreach ($partners as $partner) {
            $shipments = $partner->shipments;

            if ($shipments->count() > 0) {
                \Log::info("📧 Sending email to: {$partner->email}");
                Mail::to($partner->email)->send(new DeliveryPartnerReportMail($shipments));
                \Log::info("✅ Email sent to: {$partner->email}");
            } else {
                \Log::info(" No shipments for: {$partner->email}");
            }
        }

        $this->info('Delivery partner reports sent.');
}

    }

