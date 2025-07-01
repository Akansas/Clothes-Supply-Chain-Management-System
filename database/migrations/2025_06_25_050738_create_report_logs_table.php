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
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type'); // sales_report, inventory_report, vendor_report, etc.
            $table->string('format'); // pdf, email, csv
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('report_parameters')->nullable(); // Date range, filters, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
};
