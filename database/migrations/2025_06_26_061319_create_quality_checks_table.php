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
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->string('check_type')->nullable();
            $table->string('check_point')->nullable();
            $table->unsignedBigInteger('inspector_id')->nullable();
            $table->integer('sample_size')->nullable();
            $table->integer('defects_found')->nullable();
            $table->json('defect_types')->nullable();
            $table->integer('quality_score')->nullable();
            $table->string('pass_fail')->nullable();
            $table->dateTime('check_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->boolean('recheck_required')->default(false);
            $table->dateTime('recheck_date')->nullable();
            $table->unsignedBigInteger('recheck_inspector_id')->nullable();
            $table->string('recheck_result')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->timestamps();

            $table->foreign('production_order_id')->references('id')->on('production_orders')->cascadeOnDelete();
            $table->foreign('inspector_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('recheck_inspector_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_checks');
    }
};
