<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;

class FixProductManufacturers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-manufacturers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix products with invalid manufacturer references';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for products with invalid manufacturer references...');

        // Find products with manufacturer_id but no valid manufacturer
        $orphanedProducts = Product::whereNotNull('manufacturer_id')
            ->whereDoesntHave('manufacturer')
            ->get();

        if ($orphanedProducts->count() > 0) {
            $this->warn("Found {$orphanedProducts->count()} products with invalid manufacturer references:");
            
            foreach ($orphanedProducts as $product) {
                $this->line("- Product ID {$product->id}: {$product->name} (Manufacturer ID: {$product->manufacturer_id})");
            }

            if ($this->confirm('Do you want to remove the manufacturer_id from these products?')) {
                $updated = Product::whereNotNull('manufacturer_id')
                    ->whereDoesntHave('manufacturer')
                    ->update(['manufacturer_id' => null]);

                $this->info("Updated {$updated} products - removed invalid manufacturer references.");
            }
        } else {
            $this->info('No products with invalid manufacturer references found.');
        }

        // Find products without manufacturer_id that should have one
        $productsWithoutManufacturer = Product::whereNull('manufacturer_id')
            ->whereNull('supplier_id')
            ->where('type', 'finished_product')
            ->get();

        if ($productsWithoutManufacturer->count() > 0) {
            $this->warn("Found {$productsWithoutManufacturer->count()} finished products without manufacturer:");
            
            foreach ($productsWithoutManufacturer as $product) {
                $this->line("- Product ID {$product->id}: {$product->name}");
            }

            // Find a valid manufacturer to assign
            $validManufacturer = User::whereHas('role', function($query) {
                $query->where('name', 'manufacturer');
            })->first();

            if ($validManufacturer && $this->confirm("Do you want to assign these products to manufacturer: {$validManufacturer->name}?")) {
                $updated = Product::whereNull('manufacturer_id')
                    ->whereNull('supplier_id')
                    ->where('type', 'finished_product')
                    ->update(['manufacturer_id' => $validManufacturer->id]);

                $this->info("Updated {$updated} products - assigned to manufacturer: {$validManufacturer->name}");
            }
        }

        $this->info('Product manufacturer fix completed!');
    }
} 