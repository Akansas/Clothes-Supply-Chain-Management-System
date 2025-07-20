<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendSupplierReport;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reports:send-supplier')->everyFiveMinutes();
        $schedule->command('send:delivery-partner-reports')->daily();
        $schedule->command('send:warehouse-manager-reports')->daily();
        $schedule->command('send:manufacturer-reports')->daily();
        
                     

    }
    
    

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    protected $commands = [
        SendSupplierReport::class,
        \App\Console\Commands\SendAdminSampleReport::class,
        \App\Console\Commands\ScheduleFacilityVisitsForAllVendors::class,
        \App\Console\Commands\FixProductInventory::class,
    ];


}
