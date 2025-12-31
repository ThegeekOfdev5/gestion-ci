<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends BaseTenant
{
    use HasDomains, SoftDeletes;

    // 🔥 NE PAS implémenter TenantWithDatabase pour single DB

    protected $fillable = [
        'id',
        'name',
        'data',
        'subscription_plan',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'billing_email',
        'payment_method',
        'last_payment_at',
        'next_billing_date',
    ];

    protected $casts = [
        'data' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_billing_date' => 'date',
    ];

    // ==================== RELATIONS ====================

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function onboardingProgress()
    {
        return $this->hasOne(OnboardingProgress::class);
    }

    // ==================== METHODS ====================

    public function isSubscribed(): bool
    {
        return $this->subscription_status === 'active' || $this->onTrial();
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function trialDaysLeft(): int
    {
        if (!$this->onTrial()) {
            return 0;
        }

        return now()->diffInDays($this->trial_ends_at);
    }
}
