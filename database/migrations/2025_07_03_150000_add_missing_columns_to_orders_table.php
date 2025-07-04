<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('order_number');
            }
            if (!Schema::hasColumn('orders', 'design_id')) {
                $table->unsignedBigInteger('design_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('orders', 'quantity')) {
                $table->integer('quantity')->nullable()->after('design_id');
            }
            if (!Schema::hasColumn('orders', 'size_breakdown')) {
                $table->json('size_breakdown')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('orders', 'color_breakdown')) {
                $table->json('color_breakdown')->nullable()->after('size_breakdown');
            }
            if (!Schema::hasColumn('orders', 'fabric_requirements')) {
                $table->json('fabric_requirements')->nullable()->after('color_breakdown');
            }
            if (!Schema::hasColumn('orders', 'accessories_requirements')) {
                $table->json('accessories_requirements')->nullable()->after('fabric_requirements');
            }
            if (!Schema::hasColumn('orders', 'production_line')) {
                $table->string('production_line')->nullable()->after('accessories_requirements');
            }
            if (!Schema::hasColumn('orders', 'priority')) {
                $table->string('priority')->nullable()->after('production_line');
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('orders', 'start_date')) {
                $table->date('start_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'due_date')) {
                $table->date('due_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('orders', 'completion_date')) {
                $table->date('completion_date')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('orders', 'estimated_cost')) {
                $table->decimal('estimated_cost', 12, 2)->nullable()->after('completion_date');
            }
            if (!Schema::hasColumn('orders', 'actual_cost')) {
                $table->decimal('actual_cost', 12, 2)->nullable()->after('estimated_cost');
            }
            if (!Schema::hasColumn('orders', 'quality_score')) {
                $table->integer('quality_score')->nullable()->after('actual_cost');
            }
            if (!Schema::hasColumn('orders', 'defect_rate')) {
                $table->decimal('defect_rate', 5, 2)->nullable()->after('quality_score');
            }
            if (!Schema::hasColumn('orders', 'production_notes')) {
                $table->text('production_notes')->nullable()->after('defect_rate');
            }
            if (!Schema::hasColumn('orders', 'quality_notes')) {
                $table->text('quality_notes')->nullable()->after('production_notes');
            }
            if (!Schema::hasColumn('orders', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable()->after('quality_notes');
            }
            if (!Schema::hasColumn('orders', 'supervisor_id')) {
                $table->unsignedBigInteger('supervisor_id')->nullable()->after('assigned_to');
            }
            if (!Schema::hasColumn('orders', 'is_rush_order')) {
                $table->boolean('is_rush_order')->default(false)->after('supervisor_id');
            }
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('is_rush_order');
            }
            if (!Schema::hasColumn('orders', 'retailer_id')) {
                $table->unsignedBigInteger('retailer_id')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('orders', 'manufacturer_id')) {
                $table->unsignedBigInteger('manufacturer_id')->nullable()->after('retailer_id');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'product_id', 'design_id', 'quantity', 'size_breakdown', 'color_breakdown', 'fabric_requirements',
                'accessories_requirements', 'production_line', 'priority', 'status', 'start_date', 'due_date',
                'completion_date', 'estimated_cost', 'actual_cost', 'quality_score', 'defect_rate',
                'production_notes', 'quality_notes', 'assigned_to', 'supervisor_id', 'is_rush_order',
                'notes', 'retailer_id', 'manufacturer_id'
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}; 