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
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('raw_material_id')->nullable();
            $table->unsignedBigInteger('finished_good_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('adjustment_type', ['increase', 'decrease']);
            $table->integer('quantity');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('set null');
            $table->foreign('finished_good_id')->references('id')->on('finished_goods')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
