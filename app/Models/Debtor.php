<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debtor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'contact_id', 'chapter_id', 'transaction_id',
        'amount', 'paid_amount', 'balance', 'date_given', 'due_date',
        'status', 'description', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'date_given' => 'date',
        'due_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($debtor) {
            $debtor->balance = $debtor->amount - $debtor->paid_amount;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtorPayment::class);
    }

    /**
     * Add a payment and update balances
     */
    public function addPayment(array $data): DebtorPayment
    {
        $payment = $this->payments()->create($data);

        // Update paid amount and balance
        $this->paid_amount += $payment->amount;
        $this->balance = $this->amount - $this->paid_amount;

        // Update status
        if ($this->balance <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        }

        $this->save();

        return $payment;
    }

    /**
     * Route model binding with user scoping
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    /**
     * Check if debt is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->balance > 0;
    }
}
