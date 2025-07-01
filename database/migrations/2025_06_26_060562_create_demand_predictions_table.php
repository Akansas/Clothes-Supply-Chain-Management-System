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
        Schema::create('demand_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('forecast_qty');
            $table->date('forecast_date');
            $table->string('forecast_period'); // daily, weekly, monthly
            $table->decimal('confidence_level', 3, 2); // 0.00 to 1.00
            $table->json('model_parameters')->nullable(); // Store ML model parameters
            $table->string('model_version')->nullable();
            $table->timestamp('predicted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_predictions');
    }
};
