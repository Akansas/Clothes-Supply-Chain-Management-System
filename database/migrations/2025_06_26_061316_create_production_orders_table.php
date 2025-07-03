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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('design_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->integer('quantity');
            $table->json('size_breakdown')->nullable();
            $table->json('color_breakdown')->nullable();
            $table->json('fabric_requirements')->nullable();
            $table->json('accessories_requirements')->nullable();
            $table->string('production_line')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->integer('quality_score')->nullable();
            $table->decimal('defect_rate', 5, 2)->nullable();
            $table->text('production_notes')->nullable();
            $table->text('quality_notes')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->boolean('is_rush_order')->default(false);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('retailer_id')->nullable();
            $table->timestamps();

            $table->foreign('design_id')->references('id')->on('designs')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('supervisor_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
