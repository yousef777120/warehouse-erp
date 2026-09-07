<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class StockIssue extends Model
{
    use SoftDeletes , Auditable;
    

    protected $fillable = [
        'serial',
        'issue_date',
        'warehouse_id',
        'recipient_name',
        'reference_number',
        'status',
        'created_by',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(StockIssueItem::class, 'issue_id');
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