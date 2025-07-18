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
        Schema::create('raw_material_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manufacturer_id')
                  ->constrained('manufacturers') // explicitly referencing table
                  ->onDelete('cascade');

            $table->foreignId('raw_material_id')
                  ->constrained('raw_materials') // explicitly referencing table
                  ->onDelete('cascade');

            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->date('purchased_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_purchases');
    }
};
