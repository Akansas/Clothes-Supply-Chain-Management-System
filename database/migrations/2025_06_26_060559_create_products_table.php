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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('material');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('cost', 10, 2);
            $table->string('category')->nullable();
            $table->string('unit'); // kg, pieces, liters, etc.
            $table->integer('min_stock_level')->default(0);
            $table->integer('max_stock_level')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->unsignedBigInteger('design_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('season')->nullable();
            $table->string('collection')->nullable();
            $table->string('fabric_type')->nullable();
            $table->string('care_instructions')->nullable();
            $table->unsignedTinyInteger('sustainability_rating')->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->integer('moq')->nullable();
            $table->float('weight_kg')->nullable();
            $table->json('dimensions')->nullable();
            $table->string('barcode')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('raw_material_suppliers')->nullOnDelete();
            $table->foreign('manufacturer_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('design_id')->references('id')->on('designs')->nullOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
