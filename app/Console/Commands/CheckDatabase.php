<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check database structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking database structure...');
        
        // Check if production_orders table exists
        if (Schema::hasTable('production_orders')) {
            $this->info('✓ production_orders table exists');
            
            // Get table columns
            $columns = DB::select('DESCRIBE production_orders');
            $this->info('Columns in production_orders table:');
            foreach ($columns as $column) {
                $this->line("  - {$column->Field} ({$column->Type})");
            }
            
            // Check specifically for manufacturer_id
            $hasManufacturerId = collect($columns)->contains('Field', 'manufacturer_id');
            if ($hasManufacturerId) {
                $this->info('✓ manufacturer_id column exists');
            } else {
                $this->error('✗ manufacturer_id column does NOT exist');
            }
        } else {
            $this->error('✗ production_orders table does not exist');
        }
        
        // Check if users table exists
        if (Schema::hasTable('users')) {
            $this->info('✓ users table exists');
            
            // Get table columns
            $columns = DB::select('DESCRIBE users');
            $this->info('Columns in users table:');
            foreach ($columns as $column) {
                $this->line("  - {$column->Field} ({$column->Type})");
            }
        } else {
            $this->error('✗ users table does not exist');
        }
        
        return 0;
    }
} 