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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Product name
            $table->string('name_ar')->nullable(); // اسم المنتج
            $table->string('sku')->nullable()->unique(); // Stock keeping unit (كود المنتج)
            $table->foreignId('main_category_id')->nullable()->constrained('main_categories')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->cascadeOnDelete();
            $table->decimal('purchase_price_per_kg', 12, 3)->nullable(); // سعر الشراء للكيلو
            $table->decimal('selling_price_per_kg', 12, 3)->nullable(); // سعر البيع للكيلو
            $table->decimal('current_stock_kg', 12, 3)->nullable(); // المخزون بالكيلو
            $table->decimal('minimum_stock_alert', 12, 3)->nullable(); // تنبيه نقص المخزون
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('sku');
            $table->index('main_category_id');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
