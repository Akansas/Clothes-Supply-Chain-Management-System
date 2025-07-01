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
        Schema::create('production_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->string('stage_name');
            $table->integer('stage_order')->nullable();
            $table->decimal('estimated_duration_hours', 6, 2)->nullable();
            $table->decimal('actual_duration_hours', 6, 2)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->integer('quality_score')->nullable();
            $table->integer('defect_count')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_critical_path')->default(false);
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->timestamps();

            $table->foreign('production_order_id')->references('id')->on('production_orders')->cascadeOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('supervisor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_stages');
    }
};
