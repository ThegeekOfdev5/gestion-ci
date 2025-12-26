<?php
// database/migrations/2024_01_01_000005_create_suppliers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            $table->string('type', 20)->default('company');
            $table->string('name');
            $table->string('nif', 50)->nullable();
            $table->string('rccm', 50)->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();

            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 2)->default('CI');

            $table->string('account_code', 20)->nullable();
            $table->integer('payment_terms_days')->default(30);

            $table->text('notes')->nullable();
            $table->text('tags')->nullable();

            $table->decimal('total_purchased', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
