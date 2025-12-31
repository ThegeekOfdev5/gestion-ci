<?php
// database/migrations/2024_01_01_000014_create_accounting_entries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable(); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->date('date');
            $table->string('reference', 100)->nullable();
            $table->text('description')->nullable();

            $table->string('journal', 20)->default('general');

            $table->string('document_type', 50)->nullable();
            $table->unsignedBigInteger('document_id')->nullable();

            $table->string('status', 20)->default('draft');
            $table->timestamp('posted_at')->nullable();

            $table->boolean('is_balanced')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'date']);
            $table->index(['tenant_id', 'journal']);
            $table->index(['document_type', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_entries');
    }
};
