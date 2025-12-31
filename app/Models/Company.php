<?php
// app/Models/Company.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'legal_name',
        'trade_name',
        'legal_form',
        'nif',
        'rccm',
        'account_number',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'logo_url',
        'primary_color',
        'fiscal_year_start_month',
        'currency',
        'invoice_prefix',
        'quote_prefix',
        'next_invoice_number',
        'next_quote_number',
        'invoice_footer',
        'payment_terms_days',
    ];

    protected $casts = [
        'fiscal_year_start_month' => 'integer',
        'next_invoice_number' => 'integer',
        'next_quote_number' => 'integer',
        'payment_terms_days' => 'integer',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // ==================== METHODS ====================

    public function getNextInvoiceNumber(): string
    {
        $number = $this->next_invoice_number;
        $this->increment('next_invoice_number');

        return sprintf('%s-%s-%04d', $this->invoice_prefix, date('Y'), $number);
    }

    public function getNextQuoteNumber(): string
    {
        $number = $this->next_quote_number;
        $this->increment('next_quote_number');

        return sprintf('%s-%s-%04d', $this->quote_prefix, date('Y'), $number);
    }

    public function resetInvoiceNumbering(): void
    {
        $this->update(['next_invoice_number' => 1]);
    }

    public function resetQuoteNumbering(): void
    {
        $this->update(['next_quote_number' => 1]);
    }
}
