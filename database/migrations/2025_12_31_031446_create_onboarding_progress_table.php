<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('onboarding_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Étapes onboarding
            $table->boolean('step_company_info')->default(false);
            $table->boolean('step_user_profile')->default(false);
            $table->boolean('step_company_details')->default(false);
            $table->boolean('step_subscription')->default(false);

            // Progression
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_progress');
    }
};
