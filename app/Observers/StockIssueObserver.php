<?php
namespace App\Observers;

use App\Models\StockIssue;
use App\Services\AuditLogService;

class StockIssueObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit) { $this->audit = $audit; }

    public function created(StockIssue $i): void
    {
        $this->audit->logCreate($i, "إنشاء سند صرف: {$i->serial}");
    }

    public function updating(StockIssue $i): void { $this->originals = $i->getOriginal(); }

    public function updated(StockIssue $i): void
    {
        $changes = $i->getChanges();
        unset($changes['updated_at'], $changes['confirmed_at'], $changes['confirmed_by']);

        if (isset($changes['status']) && in_array($changes['status'], ['confirmed', 'cancelled'])) {
            return;
        }

        if (empty($changes)) return;
        $this->audit->logUpdate($i, $this->originals, "تحديث سند صرف: {$i->serial}");
    }

    public function deleted(StockIssue $i): void
    {
        $this->audit->logDelete($i, "حذف سند صرف: {$i->serial}");
    }
}