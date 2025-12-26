<?php
// database/migrations/2024_01_01_000018_create_subscription_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('XOF');
            $table->string('payment_gateway', 50)->nullable();
            $table->string('transaction_id')->nullable();

            $table->string('status', 20);

            $table->json('gateway_response')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('subscription_id');
            $table->index('status');
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
