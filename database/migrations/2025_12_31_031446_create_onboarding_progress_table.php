<?php
// database/migrations/2024_01_01_000003_create_onboarding_progress_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_progress', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Step completion flags
            $table->boolean('step_welcome')->default(false);
            $table->boolean('step_company_profile')->default(false);
            $table->boolean('step_fiscal_identity')->default(false);
            $table->boolean('step_financial_setup')->default(false);
            $table->boolean('step_modules')->default(false);

            // Progress tracking
            $table->integer('current_step')->default(1);
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_progress');
    }
};
