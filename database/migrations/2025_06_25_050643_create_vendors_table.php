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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('business_type'); // raw_materials, components, etc.
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->decimal('financial_stability_score', 3, 2)->nullable(); // 0.00 to 1.00
            $table->decimal('reputation_score', 3, 2)->nullable(); // 0.00 to 1.00
            $table->decimal('compliance_score', 3, 2)->nullable(); // 0.00 to 1.00
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
