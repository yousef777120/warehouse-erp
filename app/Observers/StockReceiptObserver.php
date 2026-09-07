<?php
namespace App\Observers;

use App\Models\StockReceipt;
use App\Services\AuditLogService;

class StockReceiptObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit) { $this->audit = $audit; }

    public function created(StockReceipt $r): void
    {
        $this->audit->logCreate($r, "إنشاء سند إدخال: {$r->serial}");
    }

    public function updating(StockReceipt $r): void { $this->originals = $r->getOriginal(); }

    public function updated(StockReceipt $r): void
    {
        $changes = $r->getChanges();
        unset($changes['updated_at'], $changes['confirmed_at'], $changes['confirmed_by']);

        // حالة خاصة: تغيير الحالة (confirm/cancel) — يُسجَّل بشكل منفصل من Service
        if (isset($changes['status']) && in_array($changes['status'], ['confirmed', 'cancelled'])) {
            return; // تم التسجيل من StockMovementService
        }

        if (empty($changes)) return;
        $this->audit->logUpdate($r, $this->originals, "تحديث سند إدخال: {$r->serial}");
    }

    public function deleted(StockReceipt $r): void
    {
        $this->audit->logDelete($r, "حذف سند إدخال: {$r->serial}");
    }
}