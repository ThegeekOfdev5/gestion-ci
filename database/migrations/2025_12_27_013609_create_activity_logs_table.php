<?php
// database/migrations/2024_01_01_000020_create_activity_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Action
            $table->string('action', 50);

            // Entité concernée
            $table->string('model', 100);
            $table->unsignedBigInteger('model_id');

            // Description
            $table->text('description')->nullable();

            // Données (avant/après pour updates)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // IP & User Agent
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('tenant_id');
            $table->index('user_id');
            $table->index(['model', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
