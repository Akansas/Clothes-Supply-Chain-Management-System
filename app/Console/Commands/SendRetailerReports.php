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

        foreach ($retailStores as $store) {
            $filename = 'retailer_report_' . $store->id . '_' . now()->format('Ymd') . '.pdf';

            // You can pass more data like orders or inventory if needed
            $pdf = Pdf::loadView('reports.retailer_report', [
                'retailer' => $store,
                'orders' => $store->orders ?? [],
                'inventory' => $store->inventory ?? [],
            ]);

            Storage::put('public/reports/' . $filename, $pdf->output());

            $this->info("PDF generated for store ID {$store->id}: $filename");
        }

        return Command::SUCCESS;
}
}