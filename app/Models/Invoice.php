<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'chapter_id', 'invoice_number', 'client_name', 'client_email',
        'client_address', 'client_phone', 'invoice_date', 'due_date', 'status',
        'subtotal', 'tax_rate', 'tax_amount', 'discount_amount', 'total_amount',
        'paid_amount', 'balance_due', 'is_revenue_generated', 'notes', 'terms', 'paid_date'
    ];
    protected $casts = [
        'invoice_date' => 'date', 'due_date' => 'date', 'paid_date' => 'date',
        'subtotal' => 'decimal:2', 'tax_rate' => 'decimal:2', 'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2', 'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2', 'balance_due' => 'decimal:2',
        'is_revenue_generated' => 'boolean',
    ];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function chapter(): BelongsTo { return $this->belongsTo(Chapter::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class)->orderBy('sort_order'); }
    public function payments(): HasMany { return $this->hasMany(InvoicePayment::class); }
    public function receipts(): HasMany { return $this->hasMany(Receipt::class); }
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('amount');
        $this->tax_amount = ($this->subtotal * $this->tax_rate) / 100;
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->calculateBalanceDue();
        $this->save();
    }

    public function calculateBalanceDue(): void
    {
        $this->paid_amount = $this->payments->sum('amount');
        $this->balance_due = $this->total_amount - $this->paid_amount;

        // Update status based on payment
        if ($this->balance_due <= 0 && $this->paid_amount > 0) {
            $this->status = 'paid';
            $this->paid_date = now();
        } elseif ($this->paid_amount > 0 && $this->balance_due > 0) {
            $this->status = 'partially_paid';
        }
    }
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y'); $month = date('m'); $prefix = "INV-{$year}{$month}";
        $lastInvoice = self::where('invoice_number', 'LIKE', "{$prefix}%")->orderBy('invoice_number', 'desc')->first();
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else { $newNumber = '0001'; }
        return "{$prefix}-{$newNumber}";
    }
    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->status !== 'cancelled' && $this->due_date < now();
    }
    public function markAsPaid(): void { $this->status = 'paid'; $this->paid_date = now(); $this->save(); }

    /**
     * Add a payment to this invoice
     */
    public function addPayment(array $paymentData): InvoicePayment
    {
        $payment = $this->payments()->create($paymentData);

        // Create corresponding transaction
        if ($this->chapter_id) {
            $transaction = Transaction::create([
                'chapter_id' => $this->chapter_id,
                'type' => 'in',  // Use 'in' for cash income transactions
                'amount' => $payment->amount,
                'description' => "Payment for Invoice #{$this->invoice_number}",
                'category' => 'Invoice Payment',
                'date' => $payment->payment_date,
                'notes' => $payment->notes,
            ]);

            $payment->update(['transaction_id' => $transaction->id]);
        }

        // Generate receipt
        $payment->generateReceipt();

        // Recalculate totals and status
        $this->load('payments');
        $this->calculateBalanceDue();
        $this->save();

        return $payment;
    }

    /**
     * Generate expected revenue when invoice is created
     */
    public function generateExpectedRevenue(): void
    {
        if (!$this->is_revenue_generated && $this->chapter_id) {
            Transaction::create([
                'chapter_id' => $this->chapter_id,
                'type' => 'expected_income',
                'amount' => $this->total_amount,
                'description' => "Expected revenue from Invoice #{$this->invoice_number}",
                'category' => 'Expected Revenue',
                'date' => $this->invoice_date,
                'notes' => "Expected revenue for client: {$this->client_name}",
            ]);

            $this->update(['is_revenue_generated' => true]);
        }
    }

    /**
     * Get outstanding invoices for revenue tracking
     */
    public static function getOutstandingInvoices($userId = null)
    {
        $query = self::with(['chapter', 'payments'])
                    ->where('status', '!=', 'paid')
                    ->where('status', '!=', 'cancelled');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    /**
     * Get expected revenue amount
     */
    public function getExpectedRevenueAttribute(): float
    {
        return (float) $this->balance_due;
    }
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray', 'sent' => 'blue', 'paid' => 'green',
            'partially_paid' => 'yellow', 'overdue' => 'red', 'cancelled' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Boot method to handle auto-linking to Financial Chapter
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            // Auto-link to Financial Chapter if no chapter is specified
            if (!$invoice->chapter_id && $invoice->user_id) {
                $financialChapter = Chapter::firstOrCreate(
                    ['user_id' => $invoice->user_id, 'name' => 'Financial'],
                    ['description' => 'Default financial chapter for invoices and revenue tracking']
                );
                $invoice->chapter_id = $financialChapter->id;
            }
        });

        static::created(function ($invoice) {
            // Generate expected revenue after creation if total > 0
            if ($invoice->total_amount > 0) {
                $invoice->generateExpectedRevenue();
            }
        });

        // Cascade soft delete to related models
        static::deleting(function ($invoice) {
            // Soft delete all invoice items
            $invoice->items()->delete();

            // Soft delete all invoice payments (and their related transactions)
            foreach ($invoice->payments as $payment) {
                // Soft delete the associated transaction if exists
                if ($payment->transaction_id) {
                    Transaction::find($payment->transaction_id)?->delete();
                }
                $payment->delete();
            }

            // Soft delete all receipts
            $invoice->receipts()->delete();
        });

        // Restore related models when invoice is restored
        static::restoring(function ($invoice) {
            // Restore all invoice items
            $invoice->items()->withTrashed()->restore();

            // Restore all invoice payments and their transactions
            foreach ($invoice->payments()->withTrashed()->get() as $payment) {
                // Restore the associated transaction if exists
                if ($payment->transaction_id) {
                    Transaction::withTrashed()->find($payment->transaction_id)?->restore();
                }
                $payment->restore();
            }

            // Restore all receipts
            $invoice->receipts()->withTrashed()->restore();
        });
    }

    /**
     * Retrieve the model for a bound value (route model binding with user scoping)
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }
}
