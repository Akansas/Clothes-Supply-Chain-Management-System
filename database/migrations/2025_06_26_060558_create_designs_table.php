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
        Schema::create('designs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('design_code')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('designer_id')->nullable();
            $table->unsignedBigInteger('collection_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('season')->nullable();
            $table->year('year')->nullable();
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->string('target_gender')->nullable();
            $table->string('target_age_group')->nullable();
            $table->json('style_tags')->nullable();
            $table->json('fabric_requirements')->nullable();
            $table->json('color_palette')->nullable();
            $table->json('size_range')->nullable();
            $table->json('technical_specs')->nullable();
            $table->string('sample_status')->nullable();
            $table->string('production_status')->nullable();
            $table->decimal('cost_estimate', 10, 2)->nullable();
            $table->decimal('retail_price_target', 10, 2)->nullable();
            $table->integer('sustainability_score')->nullable();
            $table->json('image_urls')->nullable();
            $table->json('design_files')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('designer_id')->references('id')->on('users')->nullOnDelete();
            // $table->foreign('collection_id')->references('id')->on('collections');
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
