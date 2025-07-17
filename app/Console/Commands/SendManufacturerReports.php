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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        foreach ($manufacturers as $manufacturer) {
            $orders = $manufacturer->productionOrders()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->with('product')
                ->get();

            $pdf = Pdf::loadView('reports.manufacturer_report', [
                'manufacturer' => $manufacturer,
                'productionOrders' => $orders,
                'date' => now()->format('F Y'),
            ]);

            $filename = 'manufacturer_report_' . $manufacturer->id . '_' . now()->format('Ym') . '.pdf';
            Storage::put("public/reports/$filename", $pdf->output());

            $this->info("Saved report: $filename");
        }

        return Command::SUCCESS;
            }
}