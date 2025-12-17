<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'contact_id',
        'subscription_id',
        'opportunity_id',
        'user_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'currency',
        'issue_date',
        'due_date',
        'paid_date',
        'status',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'notes',
        'terms',
        'is_recurring',
        'recurring_period',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];

    // Relations
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'sent' => 'Envoyée',
            'paid' => 'Payée',
            'partial' => 'Paiement partiel',
            'overdue' => 'En retard',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'zinc',
            'sent' => 'blue',
            'paid' => 'green',
            'partial' => 'yellow',
            'overdue' => 'red',
            'cancelled' => 'zinc',
            default => 'zinc',
        };
    }

    public function getAmountDueAttribute()
    {
        return $this->total - $this->amount_paid;
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && !in_array($this->status, ['paid', 'cancelled']);
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) return 0;
        return now()->diffInDays($this->due_date);
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Espèces',
            'transfer' => 'Virement bancaire',
            'check' => 'Chèque',
            'card' => 'Carte bancaire',
            'ccp' => 'CCP',
            default => $this->payment_method,
        };
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->whereNotIn('status', ['paid', 'cancelled'])
                    ->where('due_date', '<', now());
            });
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNotIn('status', ['paid', 'cancelled']);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeForContact($query, $contactId)
    {
        return $query->where('contact_id', $contactId);
    }

    public function scopeForSubscription($query, $subscriptionId)
    {
        return $query->where('subscription_id', $subscriptionId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('issue_date', now()->year);
    }

    // Static Methods
    public static function generateInvoiceNumber()
    {
        $year = now()->format('Y');
        $lastInvoice = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'FAC-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Methods
    public function markAsPaid($paymentMethod = null, $paymentReference = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'amount_paid' => $this->total,
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    public function addPayment($amount, $paymentMethod = null, $paymentReference = null)
    {
        $newAmountPaid = $this->amount_paid + $amount;
        
        $this->update([
            'amount_paid' => $newAmountPaid,
            'payment_method' => $paymentMethod ?? $this->payment_method,
            'payment_reference' => $paymentReference ?? $this->payment_reference,
            'status' => $newAmountPaid >= $this->total ? 'paid' : 'partial',
            'paid_date' => $newAmountPaid >= $this->total ? now() : null,
        ]);
    }

    public function send()
    {
        $this->update(['status' => 'sent']);
    }

    public function markAsOverdue()
    {
        if ($this->due_date < now() && !in_array($this->status, ['paid', 'cancelled'])) {
            $this->update(['status' => 'overdue']);
        }
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function recalculateTotals()
    {
        $subtotal = $this->items->sum('total');
        $taxAmount = $this->items->sum(function ($item) {
            return $item->total * ($item->tax_rate / 100);
        });

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount - $this->discount_amount,
        ]);
    }
}
