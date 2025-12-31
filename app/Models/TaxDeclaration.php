<?php

// app/Models/TaxDeclaration.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TaxDeclaration extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'type',
        'period_start',
        'period_end',
        'period_label',
        'data',
        'status',
        'submission_deadline',
        'submitted_at',
        'paid_at',
        'amount_due',
        'amount_paid',
        'pdf_url',
        'xml_url',
        'notes',
        'created_by',
        'submitted_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'data' => 'array',
        'submission_deadline' => 'date',
        'submitted_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // ==================== SCOPES ====================

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', 'submitted');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('submission_deadline', '<', now())
            ->where('status', 'draft');
    }

    public function scopeVat(Builder $query): Builder
    {
        return $query->where('type', 'like', 'vat%');
    }

    // ==================== ACCESSORS ====================

    public function getIsOverdueAttribute(): bool
    {
        return $this->submission_deadline < now() && $this->status === 'draft';
    }

    public function getDaysUntilDeadlineAttribute(): int
    {
        if ($this->is_overdue) {
            return 0;
        }
        return now()->diffInDays($this->submission_deadline);
    }

    public function getFormattedAmountDueAttribute(): string
    {
        return number_format($this->amount_due, 0, ',', ' ') . ' F CFA';
    }

    // ==================== METHODS ====================

    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
        ]);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'amount_paid' => $this->amount_due,
        ]);
    }
}
