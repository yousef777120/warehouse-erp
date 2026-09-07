<?php
namespace App\Observers;

use App\Models\Item;
use App\Services\AuditLogService;

class ItemObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit) { $this->audit = $audit; }

    public function created(Item $i): void { $this->audit->logCreate($i, "إنشاء صنف: {$i->name}"); }
    public function updating(Item $i): void { $this->originals = $i->getOriginal(); }
    public function updated(Item $i): void
    {
        if (empty(array_diff_key($i->getChanges(), array_flip(['updated_at'])))) return;
        $this->audit->logUpdate($i, $this->originals, "تحديث صنف: {$i->name}");
    }
    public function deleted(Item $i): void { $this->audit->logDelete($i, "حذف صنف: {$i->name}"); }
}