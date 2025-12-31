<?php
// database/migrations/2024_01_01_000010_create_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained();

            // Identification
            $table->string('invoice_number', 50);
            $table->string('reference', 100)->nullable();

            // Dates
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('paid_at')->nullable();

            // Montants
            $table->decimal('subtotal_ht', 15, 2);
            $table->decimal('total_vat', 15, 2);
            $table->decimal('total_ttc', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);

            // Paiement
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->nullable();

            // Statut
            $table->string('status', 20)->default('draft');

            // Contenu
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();

            // Métadonnées
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('reminded_at')->nullable();

            // Relations
            $table->unsignedBigInteger('quote_id')->nullable();
            $table->unsignedBigInteger('accounting_entry_id')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'invoice_number']);
            $table->index('tenant_id');
            $table->index('customer_id');
            $table->index(['tenant_id', 'status']);
            $table->index('due_date');
            $table->index(['tenant_id', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
