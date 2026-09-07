<?php
namespace App\Observers;

use App\Models\User;
use App\Services\AuditLogService;

class UserObserver
{
    protected AuditLogService $audit;
    protected array $originals = [];

    public function __construct(AuditLogService $audit)
    {
        $this->audit = $audit;
    }

    public function created(User $user): void
    {
        $this->audit->logCreate($user, "إنشاء مستخدم: {$user->name}");
    }

    public function updating(User $user): void
    {
        $this->originals = $user->getOriginal();
    }

    public function updated(User $user): void
    {
        // لا نسجل تحديث last_login أو remember_token
        $ignored = ['updated_at', 'last_login_at', 'remember_token'];
        $changed = array_diff_key($user->getChanges(), array_flip($ignored));
        if (empty($changed)) return;

        $this->audit->logUpdate($user, $this->originals, "تحديث مستخدم: {$user->name}");
    }

    public function deleted(User $user): void
    {
        $this->audit->logDelete($user, "حذف مستخدم: {$user->name}");
    }
}