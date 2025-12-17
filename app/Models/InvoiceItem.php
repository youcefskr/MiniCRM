<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'discount',
        'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relations
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        $discountedPrice = $this->unit_price * (1 - $this->discount / 100);
        return $discountedPrice * $this->quantity;
    }

    public function getTaxAmountAttribute()
    {
        return $this->subtotal * ($this->tax_rate / 100);
    }

    public function getTotalWithTaxAttribute()
    {
        return $this->subtotal + $this->tax_amount;
    }

    // Boot method pour auto-calcul
    protected static function booted()
    {
        static::saving(function ($item) {
            $discountedPrice = $item->unit_price * (1 - $item->discount / 100);
            $item->total = $discountedPrice * $item->quantity;
        });

        static::saved(function ($item) {
            $item->invoice->recalculateTotals();
        });

        static::deleted(function ($item) {
            $item->invoice->recalculateTotals();
        });
    }
}
