<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReceiptItem extends Model
{
    protected $fillable = [
        'receipt_id',
        'item_id',
        'quantity',
        'unit_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:3',
    ];

    public function receipt()
    {
        return $this->belongsTo(StockReceipt::class, 'receipt_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->quantity * (float) $this->unit_price;
    }
}