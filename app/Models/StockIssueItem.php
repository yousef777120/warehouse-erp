<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIssueItem extends Model
{
    protected $fillable = [
        'issue_id',
        'item_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function issue()
    {
        return $this->belongsTo(StockIssue::class, 'issue_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}