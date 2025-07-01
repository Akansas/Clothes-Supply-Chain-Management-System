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
        Schema::table('production_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('production_orders', 'manufacturer_id')) {
                $table->unsignedBigInteger('manufacturer_id')->nullable()->after('product_id');
                $table->foreign('manufacturer_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            if (Schema::hasColumn('production_orders', 'manufacturer_id')) {
                $table->dropForeign(['manufacturer_id']);
                $table->dropColumn('manufacturer_id');
            }
        });
    }
};
