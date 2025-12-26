<?php
// database/migrations/2024_01_01_000011_create_invoice_lines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained();

            $table->integer('line_order')->default(0);

            $table->text('description');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);

            $table->decimal('vat_rate', 5, 2)->default(18.00);
            $table->decimal('vat_amount', 15, 2);

            $table->decimal('total_ht', 15, 2);
            $table->decimal('total_ttc', 15, 2);

            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);

            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
