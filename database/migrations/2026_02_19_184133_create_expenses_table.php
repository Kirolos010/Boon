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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->cascadeOnDelete();
            $table->decimal('amount', 12, 3)->nullable(); // المبلغ
            $table->date('expense_date')->nullable(); // تاريخ المصروف
            $table->text('description')->nullable(); // الوصف
            $table->string('reference')->nullable(); // رقم الوثيقة إن وجدت
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('expense_date');
            $table->index('expense_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
