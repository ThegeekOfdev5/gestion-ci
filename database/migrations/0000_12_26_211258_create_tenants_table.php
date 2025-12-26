<?php
// database/migrations/2024_01_01_000001_create_tenants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain', 100)->unique()->nullable();
            $table->string('database', 100)->nullable();

            // Abonnement
            $table->string('subscription_plan', 50)->nullable();
            $table->string('subscription_status', 20)->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();

            // Billing
            $table->string('billing_email')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->date('next_billing_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('domain');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
