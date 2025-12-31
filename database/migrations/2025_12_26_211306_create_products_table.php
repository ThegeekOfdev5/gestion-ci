<?php
// database/migrations/2024_01_01_000007_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('product_categories');

            // Identification
            $table->string('name');
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();

            // Type
            $table->string('type', 20)->default('product');

            // Description
            $table->text('description')->nullable();

            // Prix
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('selling_price', 15, 2);

            // TVA
            $table->decimal('vat_rate', 5, 2)->default(18.00);

            // Unité
            $table->string('unit', 20)->default('unit');

            // Stock
            $table->boolean('track_stock')->default(false);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(0);

            // Comptabilité
            $table->string('sales_account_code', 20)->nullable();
            $table->string('purchase_account_code', 20)->nullable();

            // Images
            $table->string('image_url')->nullable();
            $table->json('images')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'sku']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
