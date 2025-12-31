<?php
// app/Models/Product.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'sku',
        'barcode',
        'type',
        'description',
        'purchase_price',
        'selling_price',
        'vat_rate',
        'unit',
        'track_stock',
        'current_stock',
        'min_stock',
        'sales_account_code',
        'purchase_account_code',
        'image_url',
        'images',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'track_stock' => 'boolean',
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function invoiceLines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function quoteLines()
    {
        return $this->hasMany(QuoteLine::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeProduct(Builder $query): Builder
    {
        return $query->where('type', 'product');
    }

    public function scopeService(Builder $query): Builder
    {
        return $query->where('type', 'service');
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('track_stock', true)
            ->whereColumn('current_stock', '<=', 'min_stock');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('sku', 'ilike', "%{$search}%")
                ->orWhere('barcode', 'ilike', "%{$search}%");
        });
    }

    // ==================== ACCESSORS ====================

    public function getFormattedSellingPriceAttribute(): string
    {
        return number_format($this->selling_price, 0, ',', ' ') . ' F CFA';
    }

    public function getSellingPriceTtcAttribute(): float
    {
        return $this->selling_price * (1 + ($this->vat_rate / 100));
    }

    public function getFormattedSellingPriceTtcAttribute(): string
    {
        return number_format($this->selling_price_ttc, 0, ',', ' ') . ' F CFA';
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->track_stock && $this->current_stock <= $this->min_stock;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->track_stock && $this->current_stock <= 0;
    }

    // ==================== METHODS ====================

    public function incrementStock(float $quantity): void
    {
        if ($this->track_stock) {
            $this->increment('current_stock', $quantity);
        }
    }

    public function decrementStock(float $quantity): void
    {
        if ($this->track_stock) {
            $this->decrement('current_stock', $quantity);
        }
    }

    public function setStock(float $quantity): void
    {
        if ($this->track_stock) {
            $this->update(['current_stock' => $quantity]);
        }
    }

    public function calculateVatAmount(float $quantity = 1): float
    {
        return ($this->selling_price * $quantity) * ($this->vat_rate / 100);
    }

    public function calculateTotalHt(float $quantity = 1): float
    {
        return $this->selling_price * $quantity;
    }

    public function calculateTotalTtc(float $quantity = 1): float
    {
        return $this->calculateTotalHt($quantity) + $this->calculateVatAmount($quantity);
    }
}
