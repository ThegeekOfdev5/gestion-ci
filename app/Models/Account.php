<?php

// app/Models/Account.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    protected $fillable = [
        'tenant_id',
        'code',
        'label',
        'type',
        'class',
        'is_system',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function entryLines()
    {
        return $this->hasMany(AccountingEntryLine::class);
    }

    // ==================== SCOPES ====================

    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_system', false);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByClass(Builder $query, string $class): Builder
    {
        return $query->where('class', $class);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeAssets(Builder $query): Builder
    {
        return $query->where('type', 'asset');
    }

    public function scopeLiabilities(Builder $query): Builder
    {
        return $query->where('type', 'liability');
    }

    public function scopeEquity(Builder $query): Builder
    {
        return $query->where('type', 'equity');
    }

    public function scopeRevenue(Builder $query): Builder
    {
        return $query->where('type', 'revenue');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    // ==================== ACCESSORS ====================

    public function getFullCodeAttribute(): string
    {
        return $this->code;
    }

    public function getFullLabelAttribute(): string
    {
        return "{$this->code} - {$this->label}";
    }

    // ==================== METHODS ====================

    public function getBalance($startDate = null, $endDate = null): float
    {
        $query = $this->entryLines()
            ->whereHas('entry', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted');

                if ($startDate) {
                    $q->where('date', '>=', $startDate);
                }

                if ($endDate) {
                    $q->where('date', '<=', $endDate);
                }
            });

        $debit = $query->sum('debit');
        $credit = $query->sum('credit');

        // Pour les comptes d'actif et de charges, le solde est Débit - Crédit
        if (in_array($this->type, ['asset', 'expense'])) {
            return $debit - $credit;
        }

        // Pour les comptes de passif, capitaux propres et produits, le solde est Crédit - Débit
        return $credit - $debit;
    }

    public function isDebitAccount(): bool
    {
        return in_array($this->type, ['asset', 'expense']);
    }

    public function isCreditAccount(): bool
    {
        return in_array($this->type, ['liability', 'equity', 'revenue']);
    }
}
