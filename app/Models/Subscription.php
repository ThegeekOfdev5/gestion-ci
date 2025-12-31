<?php
// app/Models/Subscription.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'plan',
        'billing_cycle',
        'amount',
        'currency',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'auto_renew',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'date',
        'current_period_end' => 'date',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    // ==================== ACCESSORS ====================

    public function getPlanNameAttribute(): string
    {
        return match ($this->plan) {
            'starter' => 'Starter',
            'essentiel' => 'Essentiel',
            'business' => 'Business',
            'premium' => 'Premium',
            default => ucfirst($this->plan),
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' F CFA';
    }

    // ==================== METHODS ====================

    public function isActive(): bool
    {
        return $this->status === 'active' || $this->onTrial();
    }

    public function onTrial(): bool
    {
        return $this->status === 'trialing' &&
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture();
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }

    public function resume(): void
    {
        $this->update([
            'status' => 'active',
            'cancelled_at' => null,
            'auto_renew' => true,
        ]);
    }

    public function renew(): void
    {
        $periodStart = $this->current_period_end->addDay();
        $periodEnd = $this->billing_cycle === 'yearly'
            ? $periodStart->copy()->addYear()
            : $periodStart->copy()->addMonth();

        $this->update([
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
            'status' => 'active',
        ]);
    }
}
