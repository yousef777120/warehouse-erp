<?php
namespace App\Observers;

use App\Models\StockTransfer;
use App\Services\AuditLogService;

class StockTransferObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit) { $this->audit = $audit; }

    public function created(StockTransfer $t): void
    {
        $this->audit->logCreate($t, "إنشاء تحويل: {$t->serial}");
    }

    public function updating(StockTransfer $t): void { $this->originals = $t->getOriginal(); }

    public function updated(StockTransfer $t): void
    {
        $changes = $t->getChanges();
        unset($changes['updated_at'], $changes['sent_at'], $changes['sent_by'],
              $changes['received_at'], $changes['received_by']);

        if (isset($changes['status']) && in_array($changes['status'], ['in_transit', 'received', 'cancelled'])) {
            return;
        }

        if (empty($changes)) return;
        $this->audit->logUpdate($t, $this->originals, "تحديث تحويل: {$t->serial}");
    }

    public function deleted(StockTransfer $t): void
    {
        $this->audit->logDelete($t, "حذف تحويل: {$t->serial}");
    }
}