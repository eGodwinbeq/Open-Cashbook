<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'chapter_id',
        'type',
        'amount',
        'description',
        'category',
        'date',
        'notes'
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
