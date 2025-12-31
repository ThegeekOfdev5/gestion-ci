<?php
// database/migrations/2024_01_01_000004_create_customers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Type
            $table->string('type', 20)->default('company');

            // Informations
            $table->string('name');
            $table->string('nif', 50)->nullable();
            $table->string('rccm', 50)->nullable();

            // Contact
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();

            // Adresse
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 2)->default('CI');

            // Comptabilité
            $table->string('account_code', 20)->nullable();
            $table->integer('payment_terms_days')->default(30);
            $table->decimal('credit_limit', 15, 2)->nullable();

            // Métadonnées
            $table->text('notes')->nullable();
            $table->text('tags')->nullable();

            // Stats
            $table->decimal('total_invoiced', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'name']);
            $table->index('nif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
