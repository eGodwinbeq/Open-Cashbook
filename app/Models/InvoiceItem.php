<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'description', 'quantity', 'unit_price', 'amount', 'sort_order'];
    protected $casts = [
        'quantity' => 'integer', 'unit_price' => 'decimal:2',
        'amount' => 'decimal:2', 'sort_order' => 'integer',
    ];
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function calculateAmount(): void
    {
        $this->amount = $this->quantity * $this->unit_price;
        $this->save();
    }
}
