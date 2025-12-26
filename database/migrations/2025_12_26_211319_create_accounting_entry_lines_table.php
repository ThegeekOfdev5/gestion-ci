<?php
// database/migrations/2024_01_01_000015_create_accounting_entry_lines_table.php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('accounting_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts');

            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);

            $table->text('description')->nullable();

            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained();

            $table->timestamps();

            $table->index('entry_id');
            $table->index('account_id');
        });

        // Ajouter contrainte check
        DB::statement('ALTER TABLE accounting_entry_lines ADD CONSTRAINT check_debit_or_credit CHECK ((debit > 0 AND credit = 0) OR (credit > 0 AND debit = 0))');
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_entry_lines');
    }
};
