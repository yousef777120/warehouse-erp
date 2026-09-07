<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class StockTransfer extends Model
{
    use SoftDeletes , Auditable;
    
    protected $fillable = [
        'serial', 'transfer_date', 'from_warehouse_id', 'to_warehouse_id',
        'status', 'created_by', 'sent_by', 'sent_at', 'received_by', 'received_at',
        'notes'
    ];
    protected $casts = [
        'transfer_date' => 'date',
        'sent_at'       => 'datetime',
        'received_at'   => 'datetime',
    ];

    public function items() { return $this->hasMany(StockTransferItem::class, 'transfer_id'); }
    public function fromWarehouse() { return $this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function toWarehouse() { return $this->belongsTo(Warehouse::class, 'to_warehouse_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function sender() { return $this->belongsTo(User::class, 'sent_by'); }
    public function receiver() { return $this->belongsTo(User::class, 'received_by'); }

    // حالات التحويل
    public function getIsDraftAttribute(): bool { return $this->status === 'draft'; }
    public function getIsInTransitAttribute(): bool { return $this->status === 'in_transit'; }
    public function getIsReceivedAttribute(): bool { return $this->status === 'received'; }
    public function getIsCancelledAttribute(): bool { return $this->status === 'cancelled'; }

    public function getTotalQuantityAttribute(): float
    {
        return $this->items->sum('quantity');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'      => 'مسودة',
            'in_transit' => 'قيد النقل',
            'received'   => 'مستلم',
            'cancelled'  => 'ملغي',
            default      => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft'      => 'badge-soft-warning',
            'in_transit' => 'badge-soft-primary',
            'received'   => 'badge-soft-success',
            'cancelled'  => 'badge-soft-danger',
            default      => 'badge-soft-secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'draft'      => 'bi-file-earmark',
            'in_transit' => 'bi-truck',
            'received'   => 'bi-check-circle-fill',
            'cancelled'  => 'bi-x-circle-fill',
            default      => 'bi-question-circle',
        };
    }
}