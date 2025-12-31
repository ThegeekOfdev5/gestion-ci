<?php
// app/Models/Invoice.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, LogsActivity, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'invoice_number',
        'reference',
        'issue_date',
        'due_date',
        'paid_at',
        'subtotal_ht',
        'total_vat',
        'total_ttc',
        'discount_amount',
        'amount_paid',
        'balance',
        'status',
        'notes',
        'terms',
        'footer',
        'sent_at',
        'viewed_at',
        'reminded_at',
        'quote_id',
        'accounting_entry_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'subtotal_ht' => 'decimal:2',
        'total_vat' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];
    // ==================== ACTIVITY LOG ====================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total_ttc', 'amount_paid', 'balance'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('line_order');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function accountingEntry()
    {
        return $this->belongsTo(AccountingEntry::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== SCOPES ====================

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['paid', 'cancelled']);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['paid', 'cancelled']);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year);
    }

    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereYear('issue_date', now()->year);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'ilike', "%{$search}%")
                ->orWhere('reference', 'ilike', "%{$search}%")
                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'ilike', "%{$search}%");
                });
        });
    }

    // ==================== ACCESSORS ====================

    public function getFormattedInvoiceNumberAttribute(): string
    {
        return $this->invoice_number;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && !in_array($this->status, ['paid', 'cancelled']);
    }

    public function getIsPartiallyPaidAttribute(): bool
    {
        return $this->amount_paid > 0 && $this->amount_paid < $this->total_ttc;
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_ttc, 0, ',', ' ') . ' F CFA';
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 0, ',', ' ') . ' F CFA';
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function getDaysUntilDueAttribute(): int
    {
        if ($this->is_overdue) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    // ==================== METHODS ====================

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsViewed(): void
    {
        if ($this->status === 'sent' && !$this->viewed_at) {
            $this->update([
                'status' => 'viewed',
                'viewed_at' => now(),
            ]);
        }
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'amount_paid' => $this->total_ttc,
            'balance' => 0,
        ]);

        // Mettre à jour les stats du client
        $this->customer->updateStats();
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function recordPayment(float $amount, string $paymentMethod, ?string $reference = null): Payment
    {
        $payment = $this->payments()->create([
            'tenant_id' => $this->tenant_id,
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            'created_by' => auth()->id(),
        ]);

        $this->updatePaymentStatus();

        return $payment;
    }

    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->payments()->sum('amount');
        $balance = $this->total_ttc - $totalPaid;

        $status = 'sent';
        if ($balance <= 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partially_paid';
        } elseif ($this->is_overdue) {
            $status = 'overdue';
        }

        $this->update([
            'amount_paid' => $totalPaid,
            'balance' => max(0, $balance),
            'status' => $status,
            'paid_at' => $balance <= 0 ? now() : null,
        ]);

        // Mettre à jour les stats du client
        $this->customer->updateStats();
    }

    public function calculateTotals(): void
    {
        $subtotalHt = $this->lines->sum('total_ht');
        $totalVat = $this->lines->sum('vat_amount');
        $totalTtc = $subtotalHt + $totalVat;

        $this->update([
            'subtotal_ht' => $subtotalHt,
            'total_vat' => $totalVat,
            'total_ttc' => $totalTtc,
            'balance' => $totalTtc - $this->amount_paid,
        ]);
    }

    public function sendReminder(): void
    {
        $this->update(['reminded_at' => now()]);

        // TODO: Envoyer email de rappel
    }

    public function duplicate(): Invoice
    {
        $newInvoice = $this->replicate();
        $newInvoice->invoice_number = $this->tenant->company->getNextInvoiceNumber();
        $newInvoice->status = 'draft';
        $newInvoice->issue_date = now();
        $newInvoice->due_date = now()->addDays($this->customer->payment_terms_days ?? 30);
        $newInvoice->sent_at = null;
        $newInvoice->viewed_at = null;
        $newInvoice->paid_at = null;
        $newInvoice->amount_paid = 0;
        $newInvoice->balance = $newInvoice->total_ttc;
        $newInvoice->created_by = auth()->id();
        $newInvoice->save();

        // Dupliquer les lignes
        foreach ($this->lines as $line) {
            $newLine = $line->replicate();
            $newLine->invoice_id = $newInvoice->id;
            $newLine->save();
        }

        return $newInvoice;
    }
}
