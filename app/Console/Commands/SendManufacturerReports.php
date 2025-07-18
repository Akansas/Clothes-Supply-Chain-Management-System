<?php
// app/Console/Commands/GenerateManufacturerMonthlyReport.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manufacturer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class SendManufacturerReports extends Command
{
    protected $signature = 'report:manufacturer-monthly';
    protected $description = 'Generate monthly PDF reports for manufacturers';

    public function handle()
    {
        $manufacturers = Manufacturer::all();
        $currentMonth = now()->format('F Y'); // e.g., July 2025


        foreach ($manufacturers as $manufacturer) {
            $orders = $manufacturer->productionOrders()
                 ->whereMonth('created_at', now()->month)
                 ->whereYear('created_at', now()->year)
                 ->with(['orderItems.product']) // Add relationships as needed
                 ->get();

            $pdf = Pdf::loadView('reports.manufacturer_report', [
                'manufacturer' => $manufacturer,
                'productionOrders' => $orders,
                'month' =>$currentMonth,
            ]);

            $filename = 'manufacturer_report_' . $manufacturer->id . '_' . now()->format('Ym') . '.pdf';
            Storage::put("public/reports/$filename", $pdf->output());

            $this->info("Saved report: $filename");
        }

        return Command::SUCCESS;
            }
}