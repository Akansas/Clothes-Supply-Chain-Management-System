<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->date('shipment_date')->nullable()->after('completed_at');
            $table->string('tracking_number')->nullable()->after('shipment_date');
        });
    }

    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropColumn('shipment_date');
            $table->dropColumn('tracking_number');
        });
    }
}; 