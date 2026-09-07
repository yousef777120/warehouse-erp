<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $fillable = ['transfer_id', 'item_id', 'quantity', 'notes'];
    protected $casts = ['quantity' => 'decimal:3'];

    public function transfer() { return $this->belongsTo(StockTransfer::class, 'transfer_id'); }
    public function item() { return $this->belongsTo(Item::class); }
}