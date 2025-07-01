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
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'company_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('company_name')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'position')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('position')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'company_name', 'position']);
        });
    }
};
