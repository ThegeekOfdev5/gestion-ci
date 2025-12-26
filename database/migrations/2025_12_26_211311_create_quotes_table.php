<?php
// database/migrations/2024_01_01_000008_create_quotes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained();

            // Identification
            $table->string('quote_number', 50);
            $table->string('reference', 100)->nullable();

            // Dates
            $table->date('issue_date');
            $table->date('valid_until');

            // Montants
            $table->decimal('subtotal_ht', 15, 2);
            $table->decimal('total_vat', 15, 2);
            $table->decimal('total_ttc', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);

            // Statut
            $table->string('status', 20)->default('draft');

            // Contenu
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();

            // Métadonnées
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();

            // Relations
            $table->unsignedBigInteger('invoice_id')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'quote_number']);
            $table->index('tenant_id');
            $table->index('customer_id');
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
