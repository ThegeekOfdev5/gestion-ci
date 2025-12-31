<?php
// database/migrations/2024_01_01_000019_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // Type
            $table->string('type', 50);

            // Contenu
            $table->string('title');
            $table->text('message')->nullable();

            // Lien
            $table->string('action_url')->nullable();

            // Statut
            $table->timestamp('read_at')->nullable();

            // Métadonnées
            $table->json('data')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'read_at']);
            $table->index('tenant_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
