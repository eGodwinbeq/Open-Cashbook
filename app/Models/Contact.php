<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'type', 'name', 'email', 'phone', 'address',
        'company', 'tax_number', 'notes'
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function debtors(): HasMany
    {
        return $this->hasMany(Debtor::class);
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
     * Get display name based on type
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'organization') {
            return $this->name;
        }
        return $this->company ? "{$this->name} ({$this->company})" : $this->name;
    }
}
