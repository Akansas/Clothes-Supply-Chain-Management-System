<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->unsignedBigInteger('manufacturer_id')->nullable()->after('product_id');
            // Optionally, add a foreign key:
            // $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('manufacturer_id');
        });
    }
}; 