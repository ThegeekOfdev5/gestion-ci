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
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Informations de base
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('business_sector')->nullable();

            // Identifiants fiscaux CI
            $table->string('nif')->nullable()->comment('Numéro Identification Fiscale');
            $table->string('rccm')->nullable()->comment('Registre Commerce');
            $table->string('ice')->nullable()->comment('Identifiant Commun Entreprise');
            $table->string('ifu')->nullable()->comment('Identifiant Fiscal Unique');
            $table->string('tax_card_number')->nullable();
            $table->string('tax_office')->nullable();

            // Adresse
            $table->text('address')->nullable();
            $table->string('city')->default('Abidjan');
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Côte d\'Ivoire');

            // Logo
            $table->string('logo')->nullable();

            // Paramètres facturation
            $table->string('invoice_prefix')->default('FAC');
            $table->unsignedInteger('last_invoice_number')->default(0);
            $table->string('quote_prefix')->default('DEV');
            $table->unsignedInteger('last_quote_number')->default(0);

            // Paramètres fiscaux
            $table->enum('tax_regime', ['reel_simplifie', 'reel_normal'])->default('reel_simplifie');
            $table->decimal('default_tax_rate', 5, 2)->default(18.00);
            $table->boolean('vat_enabled')->default(true);

            // Paramètres généraux
            $table->string('currency')->default('XOF');
            $table->string('timezone')->default('Africa/Abidjan');
            $table->string('locale')->default('fr_CI');
            $table->text('payment_terms')->nullable();
            $table->string('fiscal_year_start')->nullable();

            // Paramètres système
            $table->json('enabled_modules')->nullable();
            $table->string('selected_plan')->nullable();
            $table->unsignedInteger('user_count')->default(1);

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->unique(['tenant_id']);
            $table->index('nif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
