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
        Schema::table('delivery_partners', function (Blueprint $table) {
            // Add missing columns for profile creation
            $table->string('company_name')->nullable()->after('name');
            $table->text('address')->nullable()->after('email');
            $table->string('license_number')->nullable()->after('vehicle_number');
            $table->json('service_areas')->nullable()->after('license_number');
            $table->enum('availability', ['full_time', 'part_time', 'on_demand'])->default('full_time')->after('service_areas');
            $table->integer('experience_years')->default(0)->after('availability');
            $table->integer('total_deliveries')->default(0)->after('experience_years');
            $table->integer('completed_deliveries')->default(0)->after('total_deliveries');
            $table->integer('on_time_deliveries')->default(0)->after('completed_deliveries');
            
            // Rename 'name' to 'contact_person' if it doesn't exist
            if (!Schema::hasColumn('delivery_partners', 'contact_person')) {
                $table->renameColumn('name', 'contact_person');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_partners', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'address',
                'license_number',
                'service_areas',
                'availability',
                'experience_years',
                'total_deliveries',
                'completed_deliveries',
                'on_time_deliveries'
            ]);
        });
    }
};
