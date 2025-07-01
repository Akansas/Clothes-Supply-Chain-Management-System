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
        Schema::create('delivery_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person');
            $table->string('phone');
            $table->string('email');
            $table->string('vehicle_type'); // truck, van, motorcycle, etc.
            $table->string('vehicle_number');
            $table->integer('capacity'); // Maximum load capacity
            $table->enum('status', ['active', 'inactive', 'on_delivery'])->default('active');
            $table->decimal('rating', 3, 2)->default(0); // 0.00 to 5.00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_partners');
    }
};
