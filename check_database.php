<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking database structure...\n";

// Check deliveries table structure
echo "Checking deliveries table structure:\n";
$columns = DB::select("SHOW COLUMNS FROM deliveries");
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

// Check if supplier_id column exists in deliveries table
$supplierColumns = DB::select("SHOW COLUMNS FROM deliveries LIKE 'supplier_id'");
echo "\nChecking for supplier_id in deliveries table: " . (empty($supplierColumns) ? "NOT FOUND" : "FOUND") . "\n";

if (empty($supplierColumns)) {
    echo "supplier_id column does not exist in deliveries table. Adding it...\n";
    
    try {
        DB::statement("ALTER TABLE deliveries ADD COLUMN supplier_id BIGINT UNSIGNED NULL AFTER driver_id");
        DB::statement("ALTER TABLE deliveries ADD CONSTRAINT fk_deliveries_supplier_id FOREIGN KEY (supplier_id) REFERENCES raw_material_suppliers(id) ON DELETE SET NULL");
        echo "supplier_id column added successfully!\n";
    } catch (Exception $e) {
        echo "Error adding supplier_id column: " . $e->getMessage() . "\n";
    }
} else {
    echo "supplier_id column already exists in deliveries table.\n";
}

// Check if supplier_id column exists in orders table
$supplierColumns = DB::select("SHOW COLUMNS FROM orders LIKE 'supplier_id'");
echo "\nChecking for supplier_id in orders table: " . (empty($supplierColumns) ? "NOT FOUND" : "FOUND") . "\n";

if (empty($supplierColumns)) {
    echo "supplier_id column does not exist in orders table. Adding it...\n";
    
    try {
        DB::statement("ALTER TABLE orders ADD COLUMN supplier_id BIGINT UNSIGNED NULL AFTER user_id");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT fk_orders_supplier_id FOREIGN KEY (supplier_id) REFERENCES raw_material_suppliers(id) ON DELETE SET NULL");
        echo "supplier_id column added successfully to orders table!\n";
    } catch (Exception $e) {
        echo "Error adding supplier_id column to orders: " . $e->getMessage() . "\n";
    }
} else {
    echo "supplier_id column already exists in orders table.\n";
}

echo "\nDatabase check complete!\n"; 