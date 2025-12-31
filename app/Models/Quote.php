<?php
// app/Models/Quote.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'quote_number',
        'reference',
        'issue_date',
        'valid_until',
        'subtotal_ht',
        'total_vat',
        'total_ttc',
        'discount_amount',
        'status',
        'notes',
        'terms',
        'footer',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'declined_at',
        'invoice_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'subtotal_ht' => 'decimal:2',
        'total_vat' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

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
        return $this->hasMany(QuoteLine::class)->orderBy('line_order');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
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

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'accepted');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('valid_until', '<', now())
            ->whereIn('status', ['draft', 'sent', 'viewed']);
    }

    // ==================== ACCESSORS ====================

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until < now() && in_array($this->status, ['draft', 'sent', 'viewed']);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_ttc, 0, ',', ' ') . ' F CFA';
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

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function decline(): void
    {
        $this->update([
            'status' => 'declined',
            'declined_at' => now(),
        ]);
    }

    public function convertToInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'tenant_id' => $this->tenant_id,
            'customer_id' => $this->customer_id,
            'invoice_number' => $this->tenant->company->getNextInvoiceNumber(),
            'reference' => $this->reference,
            'issue_date' => now(),
            'due_date' => now()->addDays($this->customer->payment_terms_days ?? 30),
            'subtotal_ht' => $this->subtotal_ht,
            'total_vat' => $this->total_vat,
            'total_ttc' => $this->total_ttc,
            'discount_amount' => $this->discount_amount,
            'balance' => $this->total_ttc,
            'status' => 'draft',
            'notes' => $this->notes,
            'terms' => $this->terms,
            'footer' => $this->footer,
            'quote_id' => $this->id,
            'created_by' => auth()->id(),
        ]);

        // Copier les lignes
        foreach ($this->lines as $line) {
            $invoice->lines()->create([
                'product_id' => $line->product_id,
                'line_order' => $line->line_order,
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price,
                'vat_rate' => $line->vat_rate,
                'vat_amount' => $line->vat_amount,
                'total_ht' => $line->total_ht,
                'total_ttc' => $line->total_ttc,
                'discount_percent' => $line->discount_percent,
                'discount_amount' => $line->discount_amount,
            ]);
        }

        $this->update([
            'status' => 'converted',
            'invoice_id' => $invoice->id,
        ]);

        return $invoice;
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
        ]);
    }
}
