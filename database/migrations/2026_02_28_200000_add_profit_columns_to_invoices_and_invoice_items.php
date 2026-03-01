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
        // Add profit columns to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total_cost', 14, 3)->nullable()->default(0)->after('total')->comment('التكلفة الإجمالية');
            $table->decimal('gross_profit', 14, 3)->nullable()->default(0)->after('total_cost')->comment('الربح الإجمالي');
        });

        // Add profit columns to invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('cost_price_per_kg', 12, 3)->nullable()->default(0)->after('unit_price')->comment('تكلفة الشراء لكل كج');
            $table->decimal('item_profit', 14, 3)->nullable()->default(0)->after('cost_price_per_kg')->comment('ربح الصنف');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['total_cost', 'gross_profit']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['cost_price_per_kg', 'item_profit']);
        });
    }
};
