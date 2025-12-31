<?php
// app/Models/Customer.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'type',
        'name',
        'nif',
        'rccm',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'postal_code',
        'country',
        'account_code',
        'payment_terms_days',
        'credit_limit',
        'notes',
        'tags',
        'total_invoiced',
        'total_paid',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'payment_terms_days' => 'integer',
        'credit_limit' => 'decimal:2',
        'total_invoiced' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCompany(Builder $query): Builder
    {
        return $query->where('type', 'company');
    }

    public function scopeIndividual(Builder $query): Builder
    {
        return $query->where('type', 'individual');
    }

    public function scopeWithBalance(Builder $query): Builder
    {
        return $query->where('balance', '>', 0);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('phone', 'ilike', "%{$search}%")
                ->orWhere('nif', 'ilike', "%{$search}%");
        });
    }

    // ==================== ACCESSORS ====================

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 0, ',', ' ') . ' F CFA';
    }

    public function getHasOutstandingBalanceAttribute(): bool
    {
        return $this->balance > 0;
    }

    // ==================== METHODS ====================

    public function updateStats(): void
    {
        $this->total_invoiced = $this->invoices()->sum('total_ttc');
        $this->total_paid = $this->invoices()->sum('amount_paid');
        $this->balance = $this->total_invoiced - $this->total_paid;
        $this->save();
    }

    public function getOverdueInvoices()
    {
        return $this->invoices()
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->get();
    }

    public function getTotalOverdueAmount(): float
    {
        return $this->getOverdueInvoices()->sum('balance');
    }
}
