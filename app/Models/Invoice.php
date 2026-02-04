<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'chapter_id', 'invoice_number', 'client_name', 'client_email',
        'client_address', 'client_phone', 'invoice_date', 'due_date', 'status',
        'subtotal', 'tax_rate', 'tax_amount', 'discount_amount', 'total_amount',
        'notes', 'terms', 'paid_date'
    ];
    protected $casts = [
        'invoice_date' => 'date', 'due_date' => 'date', 'paid_date' => 'date',
        'subtotal' => 'decimal:2', 'tax_rate' => 'decimal:2', 'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2', 'total_amount' => 'decimal:2',
    ];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function chapter(): BelongsTo { return $this->belongsTo(Chapter::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class)->orderBy('sort_order'); }
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('amount');
        $this->tax_amount = ($this->subtotal * $this->tax_rate) / 100;
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
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
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray', 'sent' => 'blue', 'paid' => 'green',
            'overdue' => 'red', 'cancelled' => 'orange', default => 'gray'
        };
    }
}
