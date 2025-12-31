<?php

// app/Models/AccountingEntry.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AccountingEntry extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'date',
        'reference',
        'description',
        'journal',
        'document_type',
        'document_id',
        'status',
        'posted_at',
        'is_balanced',
        'created_by',
        'posted_by',
    ];

    protected $casts = [
        'date' => 'date',
        'posted_at' => 'datetime',
        'is_balanced' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lines()
    {
        return $this->hasMany(AccountingEntryLine::class, 'entry_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // ==================== SCOPES ====================

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted(Builder $query): Builder
    {
        return $query->where('status', 'posted');
    }

    public function scopeByJournal(Builder $query, string $journal): Builder
    {
        return $query->where('journal', $journal);
    }

    public function scopeByPeriod(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // ==================== METHODS ====================

    public function post(): void
    {
        if (!$this->isBalanced()) {
            throw new \Exception('L\'écriture n\'est pas équilibrée');
        }

        $this->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function isBalanced(): bool
    {
        $totalDebit = $this->lines->sum('debit');
        $totalCredit = $this->lines->sum('credit');

        return round($totalDebit, 2) === round($totalCredit, 2);
    }

    public function getTotalDebit(): float
    {
        return $this->lines->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return $this->lines->sum('credit');
    }

    protected static function booted()
    {
        static::saving(function ($entry) {
            $entry->is_balanced = $entry->isBalanced();
        });
    }
}
