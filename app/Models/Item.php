<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StockBalance;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Item extends Model
{
    use SoftDeletes , Auditable;
   

    protected $fillable = ['code', 'barcode', 'name', 'category_id', 'unit_id', 'sku', 'min_stock', 'max_stock', 'description', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
        'min_stock' => 'decimal:3',
        'max_stock' => 'decimal:3',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
    /**
 * Get the stock balances for the item.
 */
public function stockBalances(): HasMany
{
    return $this->hasMany(StockBalance::class);
}
}