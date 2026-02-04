<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'chapter_id',
        'type',
        'amount',
        'description',
        'category',
        'date',
        'notes'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
