<?php

// app/Models/InvoiceLine.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'line_order',
        'description',
        'quantity',
        'unit_price',
        'vat_rate',
        'vat_amount',
        'total_ht',
        'total_ttc',
        'discount_percent',
        'discount_amount',
    ];

    protected $casts = [
        'line_order' => 'integer',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    // ==================== RELATIONS ====================

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ==================== METHODS ====================

    public function calculateAmounts(): void
    {
        $totalHt = $this->quantity * $this->unit_price;

        // Appliquer remise si présente
        if ($this->discount_percent > 0) {
            $this->discount_amount = $totalHt * ($this->discount_percent / 100);
            $totalHt -= $this->discount_amount;
        }

        $this->total_ht = $totalHt;
        $this->vat_amount = $totalHt * ($this->vat_rate / 100);
        $this->total_ttc = $this->total_ht + $this->vat_amount;

        $this->save();
    }

    protected static function booted()
    {
        static::saved(function ($line) {
            $line->invoice->calculateTotals();
        });

        static::deleted(function ($line) {
            $line->invoice->calculateTotals();
        });
    }
}
