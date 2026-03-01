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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable()->unique(); // رقم الفاتورة
            $table->enum('type', ['regular', 'quick'])->nullable()->default('regular'); // Regular or Quick sale
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->decimal('subtotal', 14, 3)->nullable(); // الإجمالي
            $table->decimal('discount', 14, 3)->nullable()->default(0); // خصم
            $table->decimal('tax', 14, 3)->nullable()->default(0); // ضريبة
            $table->decimal('total', 14, 3)->nullable(); // الإجمالي بعد الخصم والضريبة
            $table->decimal('amount_paid', 14, 3)->nullable()->default(0); // المبلغ المدفوع
            $table->decimal('remaining_balance', 14, 3)->nullable(); // الرصيد
            $table->enum('status', ['paid', 'partial', 'unpaid'])->nullable()->default('unpaid'); // حالة الدفع
            $table->date('invoice_date')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('client_id');
            $table->index('invoice_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
