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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report_type'); // sales_report, inventory_report, vendor_report, etc.
            $table->string('frequency'); // daily, weekly, monthly
            $table->string('format'); // pdf, email, csv
            $table->time('delivery_time')->default('08:00:00');
            $table->json('report_filters')->nullable(); // Custom filters for the user
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
