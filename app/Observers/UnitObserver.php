<?php
namespace App\Observers;

use App\Models\Unit;
use App\Services\AuditLogService;

class UnitObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit) { $this->audit = $audit; }

    public function created(Unit $u): void { $this->audit->logCreate($u, "إنشاء وحدة: {$u->name}"); }
    public function updating(Unit $u): void { $this->originals = $u->getOriginal(); }
    public function updated(Unit $u): void
    {
        if (empty(array_diff_key($u->getChanges(), array_flip(['updated_at'])))) return;
        $this->audit->logUpdate($u, $this->originals, "تحديث وحدة: {$u->name}");
    }
    public function deleted(Unit $u): void { $this->audit->logDelete($u, "حذف وحدة: {$u->name}"); }
}