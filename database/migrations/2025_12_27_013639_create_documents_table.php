<?php
// database/migrations/2024_01_01_000021_create_documents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // 🔥 GARDER tenant_id (UUID)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Type de document (polymorphique)
            $table->string('documentable_type', 100)->nullable();
            $table->unsignedBigInteger('documentable_id')->nullable();

            // Fichier
            $table->string('filename');
            $table->string('original_filename')->nullable();
            $table->string('file_path', 500);
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();

            // Type
            $table->string('type', 50)->nullable();

            // Métadonnées
            $table->text('description')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
