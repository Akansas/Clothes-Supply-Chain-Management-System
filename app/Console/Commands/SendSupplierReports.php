<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RawMaterialSupplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SendSupplierReports extends Command
{
    protected $signature = 'reports:generate-supplier-monthly';
    protected $description = 'Generate monthly purchase order PDF reports for raw material suppliers';

    public function handle()
    {
        \Log::info('📦 Starting supplier PDF report generation...');

        $suppliers = RawMaterialSupplier::with('purchaseOrders')->get();

        if ($suppliers->isEmpty()) {
            $this->info('No suppliers found.');
            return;
        }

        $reportsGenerated = 0;
        $monthName = now()->format('F Y');

        foreach ($suppliers as $supplier) {
            $monthlyOrders = $supplier->purchaseOrders()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->get();

            if ($monthlyOrders->count()) {
                $pdf = Pdf::loadView('reports.supplier_monthly_report', [
                    'supplier' => $supplier,
                    'orders' => $monthlyOrders,
                    'month' => $monthName,
                ]);

                $fileName = 'supplier_report_' . $supplier->id . '_' . now()->format('Y_m') . '.pdf';
                $path = 'reports/suppliers/' . $fileName;

                Storage::put($path, $pdf->output());

                \Log::info("✅ PDF saved: $path");
                $reportsGenerated++;
            } else {
                \Log::info("⛔ No orders for {$supplier->name} this month.");
            }
        }

        if ($reportsGenerated > 0) {
            $this->info("✅ PDF reports generated for {$reportsGenerated} supplier(s). Check storage/app/reports/suppliers/");
        } else {
            $this->info("❌ No supplier reports to generate.");
        }

        \Log::info('📤 Supplier PDF report generation finished.');
     }
}