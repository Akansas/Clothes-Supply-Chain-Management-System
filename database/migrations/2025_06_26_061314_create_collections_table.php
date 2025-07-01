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
        Schema::create('collections', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('collection_code')->unique();
            $table->text('description')->nullable();
            $table->string('season')->nullable();
            $table->year('year')->nullable();
            $table->string('theme')->nullable();
            $table->string('inspiration')->nullable();
            $table->string('target_market')->nullable();
            $table->date('launch_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('expected_revenue', 12, 2)->nullable();
            $table->unsignedBigInteger('designer_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('image_url')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('designer_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
