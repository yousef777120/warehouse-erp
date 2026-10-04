<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInvoice extends Model
{
    protected $fillable = [
        'file_path',
        'extracted_data',
        'confidence',
        'status',
        'journal_entry_id',
        'ai_notes',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'confidence' => 'decimal:2',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /** تسمية الحالة بالعربية */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'بانتظار الاعتماد',
            'approved' => 'معتمدة',
            'posted'   => 'مرحّلة',
            'rejected' => 'مرفوضة',
            default    => (string) $this->status,
        };
    }
}