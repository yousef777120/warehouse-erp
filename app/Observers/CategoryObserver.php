<?php
namespace App\Observers;

use App\Models\Category;
use App\Services\AuditLogService;

class CategoryObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit) { $this->audit = $audit; }

    public function created(Category $c): void { $this->audit->logCreate($c, "إنشاء تصنيف: {$c->name}"); }
    public function updating(Category $c): void { $this->originals = $c->getOriginal(); }
    public function updated(Category $c): void
    {
        if (empty(array_diff_key($c->getChanges(), array_flip(['updated_at'])))) return;
        $this->audit->logUpdate($c, $this->originals, "تحديث تصنيف: {$c->name}");
    }
    public function deleted(Category $c): void { $this->audit->logDelete($c, "حذف تصنيف: {$c->name}"); }
}