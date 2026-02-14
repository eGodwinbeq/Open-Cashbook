<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number', 'invoice_id', 'invoice_payment_id', 'amount',
        'receipt_date', 'client_name', 'description', 'receipt_data'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'receipt_date' => 'date',
        'receipt_data' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(InvoicePayment::class, 'invoice_payment_id');
    }

    /**
     * Generate a unique receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "RCP-{$year}{$month}";

        $lastReceipt = self::where('receipt_number', 'LIKE', "{$prefix}%")
                           ->orderBy('receipt_number', 'desc')
                           ->first();

        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt->receipt_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$newNumber}";
    }

    /**
     * Retrieve the model for a bound value (route model binding with user scoping through invoice)
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)
            ->whereHas('invoice', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->firstOrFail();
    }
}
