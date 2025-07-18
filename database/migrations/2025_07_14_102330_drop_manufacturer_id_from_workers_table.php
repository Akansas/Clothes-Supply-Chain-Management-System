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
        Schema::table('workers', function (Blueprint $table) {
            $table->dropForeign(['manufacturer_id']);
            $table->dropColumn('manufacturer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
        });
    }
};
