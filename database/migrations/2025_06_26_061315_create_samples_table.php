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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_id');
            $table->string('sample_code')->unique();
            $table->string('sample_type')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('fabric_used')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('status')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('request_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('quality_score')->nullable();
            $table->text('fit_notes')->nullable();
            $table->text('design_notes')->nullable();
            $table->text('production_notes')->nullable();
            $table->json('image_urls')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->date('approval_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->timestamps();

            $table->foreign('design_id')->references('id')->on('designs')->cascadeOnDelete();
            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
