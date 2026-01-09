<?php
// app/Models/Company.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use BelongsToTenant, SoftDeletes;

protected $fillable = [
        'tenant_id',
        'name',
        'legal_name',
        'email',
        'phone',
        'mobile',
        'business_sector',
        'logo',
        'address',
        'city',
        'postal_code',
        'country',
        'nif',
        'rccm',
        'ice',
        'ifu',
        'tax_regime',
        'tax_card_number',
        'tax_office',
        'invoice_prefix',
        'quote_prefix',
        'default_tax_rate',
        'currency',
        'timezone',
        'payment_terms',
        'fiscal_year_start',
        'vat_enabled',
        'enabled_modules',
        'selected_plan',
        'user_count',
    ];

    protected $casts = [
        'default_tax_rate' => 'float',
        'vat_enabled' => 'boolean',
        'enabled_modules' => 'array',
        'user_count' => 'integer',
    ];

    /**
     * Relation : Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Générer le prochain numéro de facture (thread-safe)
     */
    public function getNextInvoiceNumber(): string
    {
        return DB::transaction(function () {
            $this->lockForUpdate()->increment('last_invoice_number');
            $this->refresh();

            return sprintf(
                '%s-%s-%05d',
                $this->invoice_prefix,
                now()->format('Y'),
                $this->last_invoice_number
            );
        });
    }

    /**
     * Générer le prochain numéro de devis (thread-safe)
     */
    public function getNextQuoteNumber(): string
    {
        return DB::transaction(function () {
            $this->lockForUpdate()->increment('last_quote_number');
            $this->refresh();

            return sprintf(
                '%s-%s-%05d',
                $this->quote_prefix,
                now()->format('Y'),
                $this->last_quote_number
            );
        });
    }

    /**
     * Vérifier si l'entreprise a complété son profil
     */
    public function hasCompletedProfile(): bool
    {
        return !empty($this->nif)
            && !empty($this->rccm)
            && !empty($this->address);
    }

    /**
     * Obtenir le logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : null;
    }
}
