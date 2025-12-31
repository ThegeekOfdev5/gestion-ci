<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnboardingProgress extends Model
{
    use HasFactory;

    protected $table = 'onboarding_progress';

    protected $fillable = [
        'tenant_id',
        'step_company_info',
        'step_company_details',
        'step_user_profile',
        'step_subscription',
        'current_step',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'step_company_info' => 'boolean',
        'step_company_details' => 'boolean',
        'step_user_profile' => 'boolean',
        'step_subscription' => 'boolean',
        'current_step' => 'integer',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
