<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class StockReceipt extends Model
{
    use SoftDeletes , Auditable;
   

    protected $fillable = [
        'serial',
        'receipt_date',
        'warehouse_id',
        'supplier_name',
        'reference_number',
        'status',
        'created_by',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(StockReceiptItem::class, 'receipt_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}