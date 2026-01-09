<?php
// app/Models/OnboardingProgress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'step_welcome',
        'step_company_profile',
        'step_fiscal_identity',
        'step_financial_setup',
        'step_modules',
        'current_step',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'step_welcome' => 'boolean',
        'step_company_profile' => 'boolean',
        'step_fiscal_identity' => 'boolean',
        'step_fiscal_setup' => 'boolean',
        'step_modules' => 'boolean',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship with Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
