<?php
// database/migrations/2024_01_01_000012_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained();

            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50);
            $table->string('reference', 100)->nullable();

            $table->text('notes')->nullable();

            $table->unsignedBigInteger('accounting_entry_id')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index('invoice_id');
            $table->index(['tenant_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
