<?php
// database/migrations/2024_01_01_000017_create_subscriptions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->string('plan', 50);
            $table->string('billing_cycle', 20)->default('monthly');

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('XOF');

            $table->string('status', 20)->default('trialing');

            $table->timestamp('trial_ends_at')->nullable();
            $table->date('current_period_start')->nullable();
            $table->date('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->boolean('auto_renew')->default(true);

            $table->timestamps();

            $table->index('status');
            $table->index('current_period_end');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
