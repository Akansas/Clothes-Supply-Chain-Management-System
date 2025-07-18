<?Php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RetailStore;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SendRetailerReports extends Command
{
    protected $signature = 'reports:send-retailer-pdf';
    protected $description = 'Generate and store PDF reports for each retail store';

   public function handle()
{
    $retailStores = RetailStore::all();
    $currentMonth = now()->format('F Y'); // e.g., July 2025

    foreach ($retailStores as $store) {
        $filename = 'retailer_report_' . $store->id . '_' . now()->format('Ym') . '.pdf';

        // Filter only orders made this month
        $monthlyOrders = $store->orders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->with(['orderItems.product']) // Add relationships as needed
            ->get();

        $pdf = Pdf::loadView('reports.retailer_report', [
            'retailer' => $store,
            'orders' => $monthlyOrders,
            'inventory' => $store->inventory ?? [],
            'month' => $currentMonth,
        ]);

        Storage::put('public/reports/' . $filename, $pdf->output());

        $this->info("Monthly PDF generated for store ID {$store->id}: $filename");
    }

    return Command::SUCCESS;
}
}