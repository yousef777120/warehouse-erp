<?php
namespace App\Observers;

use App\Models\Warehouse;
use App\Services\AuditLogService;

class WarehouseObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit) { $this->audit = $audit; }

    public function created(Warehouse $w): void { $this->audit->logCreate($w, "إنشاء مخزن: {$w->name}"); }
    public function updating(Warehouse $w): void { $this->originals = $w->getOriginal(); }
    public function updated(Warehouse $w): void
    {
        $ignored = ['updated_at'];
        if (empty(array_diff_key($w->getChanges(), array_flip($ignored)))) return;
        $this->audit->logUpdate($w, $this->originals, "تحديث مخزن: {$w->name}");
    }
    public function deleted(Warehouse $w): void { $this->audit->logDelete($w, "حذف مخزن: {$w->name}"); }
}