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
        'nif',
        'rccm',
        'ice',
        'ifu',
        'address',
        'city',
        'postal_code',
        'country',
        'logo',
        'invoice_prefix',
        'last_invoice_number',
        'quote_prefix',
        'last_quote_number',
        'tax_regime',
        'default_tax_rate',
        'currency',
        'timezone',
        'locale',
    ];

    protected $casts = [
        'last_invoice_number' => 'integer',
        'last_quote_number' => 'integer',
        'default_tax_rate' => 'decimal:2',
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
