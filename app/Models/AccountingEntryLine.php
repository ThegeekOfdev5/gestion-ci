<?php

// app/Models/AccountingEntryLine.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AccountingEntryLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'entry_id',
        'account_id',
        'debit',
        'credit',
        'description',
        'customer_id',
        'supplier_id',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    // ==================== RELATIONS ====================

    public function entry()
    {
        return $this->belongsTo(AccountingEntry::class, 'entry_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // ==================== ACCESSORS ====================

    public function getFormattedDebitAttribute(): string
    {
        return $this->debit > 0 ? number_format($this->debit, 0, ',', ' ') : '';
    }

    public function getFormattedCreditAttribute(): string
    {
        return $this->credit > 0 ? number_format($this->credit, 0, ',', ' ') : '';
    }

    // ==================== METHODS ====================

    protected static function booted()
    {
        static::saving(function ($line) {
            // Valider qu'on a soit débit soit crédit, mais pas les deux
            if ($line->debit > 0 && $line->credit > 0) {
                throw new \Exception('Une ligne ne peut avoir à la fois un débit et un crédit');
            }

            if ($line->debit == 0 && $line->credit == 0) {
                throw new \Exception('Une ligne doit avoir soit un débit soit un crédit');
            }
        });
    }
}
