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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('capacity'); // Total capacity in units
            $table->integer('current_utilization')->default(0);
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('contact_person');
            $table->string('phone');
            $table->string('email');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
