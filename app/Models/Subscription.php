<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'contact_id',
        'opportunity_id',
        'user_id',
        'billing_cycle',
        'amount',
        'tax_rate',
        'currency',
        'start_date',
        'end_date',
        'next_billing_date',
        'next_renewal_date',
        'status',
        'auto_renew',
        'renewal_reminder_days',
        'billing_count',
        'total_billed',
        'notes',
        'terms',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'next_renewal_date' => 'date',
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_billed' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];

    // Relations
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'subscription_product')
            ->withPivot('quantity', 'unit_price', 'discount')
            ->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Accessors
    public function getTotalWithTaxAttribute()
    {
        return $this->amount * (1 + $this->tax_rate / 100);
    }

    public function getTaxAmountAttribute()
    {
        return $this->amount * ($this->tax_rate / 100);
    }

    public function getBillingCycleLabelAttribute()
    {
        return match($this->billing_cycle) {
            'monthly' => 'Mensuel',
            'quarterly' => 'Trimestriel',
            'semi_annual' => 'Semestriel',
            'annual' => 'Annuel',
            default => $this->billing_cycle,
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Actif',
            'pending' => 'En attente',
            'paused' => 'Suspendu',
            'cancelled' => 'Annulé',
            'expired' => 'Expiré',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'pending' => 'yellow',
            'paused' => 'orange',
            'cancelled' => 'red',
            'expired' => 'zinc',
            default => 'zinc',
        };
    }

    public function getDaysUntilRenewalAttribute()
    {
        if (!$this->next_renewal_date) return null;
        return now()->diffInDays($this->next_renewal_date, false);
    }

    public function getIsRenewalSoonAttribute()
    {
        if (!$this->next_renewal_date) return false;
        return $this->days_until_renewal <= $this->renewal_reminder_days && $this->days_until_renewal > 0;
    }

    public function getIsOverdueAttribute()
    {
        if (!$this->next_billing_date) return false;
        return $this->next_billing_date < now() && $this->status === 'active';
    }

    public function getMonthlyValueAttribute()
    {
        return match($this->billing_cycle) {
            'monthly' => $this->amount,
            'quarterly' => $this->amount / 3,
            'semi_annual' => $this->amount / 6,
            'annual' => $this->amount / 12,
            default => $this->amount,
        };
    }

    public function getAnnualValueAttribute()
    {
        return match($this->billing_cycle) {
            'monthly' => $this->amount * 12,
            'quarterly' => $this->amount * 4,
            'semi_annual' => $this->amount * 2,
            'annual' => $this->amount,
            default => $this->amount,
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNeedsBilling($query)
    {
        return $query->where('status', 'active')
            ->where('next_billing_date', '<=', now());
    }

    public function scopeRenewalSoon($query, $days = null)
    {
        return $query->where('status', 'active')
            ->whereNotNull('next_renewal_date')
            ->where('next_renewal_date', '<=', now()->addDays($days ?? 7))
            ->where('next_renewal_date', '>', now());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function calculateNextBillingDate()
    {
        $lastDate = $this->next_billing_date ?? $this->start_date;
        
        return match($this->billing_cycle) {
            'monthly' => Carbon::parse($lastDate)->addMonth(),
            'quarterly' => Carbon::parse($lastDate)->addMonths(3),
            'semi_annual' => Carbon::parse($lastDate)->addMonths(6),
            'annual' => Carbon::parse($lastDate)->addYear(),
            default => Carbon::parse($lastDate)->addMonth(),
        };
    }

    public function generateInvoice()
    {
        $invoiceNumber = Invoice::generateInvoiceNumber();
        
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'contact_id' => $this->contact_id,
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'subtotal' => $this->amount,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total_with_tax,
            'currency' => $this->currency,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'sent',
            'is_recurring' => true,
            'recurring_period' => $this->billing_cycle,
        ]);

        // Ajouter les lignes de facture basées sur les produits
        if ($this->products->count() > 0) {
            foreach ($this->products as $product) {
                $unitPrice = $product->pivot->unit_price;
                $quantity = $product->pivot->quantity;
                $discount = $product->pivot->discount;
                $discountedPrice = $unitPrice * (1 - $discount / 100);
                
                $invoice->items()->create([
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $this->tax_rate,
                    'discount' => $discount,
                    'total' => $discountedPrice * $quantity,
                ]);
            }
        } else {
            // Si pas de produits, créer une ligne générique
            $invoice->items()->create([
                'description' => $this->name . ' - ' . $this->billing_cycle_label,
                'quantity' => 1,
                'unit_price' => $this->amount,
                'tax_rate' => $this->tax_rate,
                'discount' => 0,
                'total' => $this->amount,
            ]);
        }

        // Mettre à jour le compteur et le total facturé
        $this->increment('billing_count');
        $this->increment('total_billed', $this->total_with_tax);
        $this->update(['next_billing_date' => $this->calculateNextBillingDate()]);

        return $invoice;
    }

    public function renew()
    {
        if (!$this->auto_renew) {
            return false;
        }

        $this->update([
            'next_renewal_date' => $this->calculateNextBillingDate(),
            'status' => 'active',
        ]);

        return true;
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);
    }

    public function resume()
    {
        $this->update(['status' => 'active']);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'end_date' => now(),
        ]);
    }
}
