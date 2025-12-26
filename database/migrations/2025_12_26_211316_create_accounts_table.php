<?php
// database/migrations/2024_01_01_000013_create_accounts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('code', 20);
            $table->string('label');
            $table->string('type', 20);
            $table->char('class', 1);

            $table->boolean('is_system')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('accounts');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index('tenant_id');
            $table->index('code');
            $table->index('class');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
