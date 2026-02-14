<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InvoicePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id', 'transaction_id', 'amount', 'payment_method',
        'payment_date', 'notes', 'reference_number'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    /**
     * Generate a receipt for this payment
     */
    public function generateReceipt(): Receipt
    {
        return Receipt::create([
            'receipt_number' => Receipt::generateReceiptNumber(),
            'invoice_id' => $this->invoice_id,
            'invoice_payment_id' => $this->id,
            'amount' => $this->amount,
            'receipt_date' => $this->payment_date,
            'client_name' => $this->invoice->client_name,
            'description' => "Payment for Invoice #{$this->invoice->invoice_number}",
            'receipt_data' => [
                'payment_method' => $this->payment_method,
                'reference_number' => $this->reference_number,
                'notes' => $this->notes,
            ]
        ]);
    }
}
