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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Client name
            $table->string('name_ar')->nullable(); // اسم العميل
            $table->string('phone')->nullable();
            $table->string('phone_2')->nullable();
            $table->text('address')->nullable();
            $table->text('address_ar')->nullable();
            $table->decimal('credit_limit', 14, 3)->nullable()->default(0); // Credit limit for client
            $table->decimal('total_debt', 14, 3)->nullable()->default(0); // Total debt amount
            $table->text('notes')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
