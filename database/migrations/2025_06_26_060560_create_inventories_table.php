<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('location_type', ['warehouse', 'retail']);
            $table->unsignedBigInteger('location_id'); // warehouse_id or retail_store_id
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('retail_store_id')->nullable()->constrained('retail_stores')->onDelete('set null');
            $table->integer('quantity');
            $table->integer('reserved_quantity')->default(0); // For pending orders
            $table->integer('available_quantity')->default(0); // quantity - reserved_quantity
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->enum('status', ['active', 'expired', 'damaged', 'quarantine'])->default('active');
            $table->timestamps();
            
            // Composite index for efficient queries
            $table->index(['location_type', 'location_id']);
            $table->index(['product_id', 'location_type', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
