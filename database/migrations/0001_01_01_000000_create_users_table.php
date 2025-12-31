<?php

// database/migrations/2024_01_01_000002_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Relation tenant
            $table->string('tenant_id');
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');

            // Authentification
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            // Informations personnelles
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();

            // Préférences
            $table->string('language')->default('fr');
            $table->boolean('phone_verified')->default(false);

            // Sécurité
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->softDeletes();

            // Timestamps
            $table->timestamps();

            // Index et contraintes d'unicité
            $table->unique(['tenant_id', 'email']);
            $table->index('tenant_id');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
