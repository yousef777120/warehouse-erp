<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /** تسمية العملية بالعربية */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created'  => 'إنشاء',
            'updated'  => 'تحديث',
            'deleted'  => 'حذف',
            default    => $this->action,
        };
    }

    /** لون الشارة حسب العملية */
    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'created'  => 'bg-success',
            'updated'  => 'bg-warning text-dark',
            'deleted'  => 'bg-danger',
            default    => 'bg-secondary',
        };
    }

    /** اسم الجدول بالعربية */
    public function getModelNameAttribute(): string
    {
        return match (class_basename($this->auditable_type)) {
            'User'          => 'مستخدم',
            'Role'          => 'دور',
            'Warehouse'     => 'مخزن',
            'Category'      => 'تصنيف',
            'Unit'          => 'وحدة',
            'Item'          => 'صنف',
            'StockReceipt'  => 'سند إدخال',
            'StockIssue'    => 'سند صرف',
            'StockTransfer' => 'تحويل مخزني',
            default         => class_basename($this->auditable_type),
        };
    }
}