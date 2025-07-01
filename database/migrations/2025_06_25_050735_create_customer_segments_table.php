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
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('segment'); // bulk_buyer, regular_customer, occasional_buyer, etc.
            $table->decimal('segment_score', 3, 2); // 0.00 to 1.00
            $table->json('segment_characteristics')->nullable(); // Store segment features
            $table->json('recommendations')->nullable(); // Personalized recommendations
            $table->string('model_version')->nullable();
            $table->timestamp('segmented_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_segments');
    }
};
