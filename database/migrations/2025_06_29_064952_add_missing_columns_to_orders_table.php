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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->unique()->after('id');
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained('users')->onDelete('cascade');
            $table->text('billing_address')->nullable()->after('shipping_address');
            $table->string('payment_method')->nullable()->after('billing_address');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_method');
            $table->timestamp('order_date')->nullable()->after('payment_status');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn([
                'order_number',
                'customer_id', 
                'billing_address',
                'payment_method',
                'payment_status',
                'order_date',
                'cancelled_at'
            ]);
        });
    }
};
