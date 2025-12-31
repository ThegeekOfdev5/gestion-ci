<?php
// database/migrations/2024_01_01_000003_create_companies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Informations légales
            $table->string('legal_name');
            $table->string('trade_name')->nullable();
            $table->string('legal_form', 50)->nullable();

            // Identifiants fiscaux CI
            $table->string('nif', 50)->nullable();
            $table->string('rccm', 50)->nullable();
            $table->string('account_number', 50)->nullable();

            // Coordonnées
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 2)->default('CI');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Branding
            $table->string('logo_url')->nullable();
            $table->string('primary_color', 7)->nullable();

            // Comptabilité
            $table->integer('fiscal_year_start_month')->default(1);
            $table->string('currency', 3)->default('XOF');

            // Paramètres facturation
            $table->string('invoice_prefix', 10)->default('FAC');
            $table->string('quote_prefix', 10)->default('DEV');
            $table->integer('next_invoice_number')->default(1);
            $table->integer('next_quote_number')->default(1);
            $table->text('invoice_footer')->nullable();
            $table->integer('payment_terms_days')->default(30);

            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
