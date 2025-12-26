<?php
// database/migrations/2024_01_01_000016_create_tax_declarations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            $table->string('type', 50);

            $table->date('period_start');
            $table->date('period_end');
            $table->string('period_label', 20)->nullable();

            $table->json('data');

            $table->string('status', 20)->default('draft');

            $table->date('submission_deadline');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->decimal('amount_due', 15, 2)->nullable();
            $table->decimal('amount_paid', 15, 2)->default(0);

            $table->string('pdf_url')->nullable();
            $table->string('xml_url')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('submitted_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'type']);
            $table->index(['period_start', 'period_end']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_declarations');
    }
};
