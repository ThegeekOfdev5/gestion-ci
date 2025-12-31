<?php
// app/Models/Supplier.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, BelongsToTenant;

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
        'notes',
        'tags',
        'total_purchased',
        'total_paid',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'payment_terms_days' => 'integer',
        'total_purchased' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('phone', 'ilike', "%{$search}%");
        });
    }

    // ==================== ACCESSORS ====================

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 0, ',', ' ') . ' F CFA';
    }
}
