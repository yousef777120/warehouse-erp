<?php
namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * تسجيل عملية عامة
     */
    public function log(
        string $action,
        Model $auditable,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): void {
        $values = [
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => get_class($auditable),
            'auditable_id'   => $auditable->id,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ];

        if ($description) {
            $values['new_values'] = array_merge($values['new_values'] ?? [], ['description' => $description]);
        }

        AuditLog::create($values);
    }

    /**
     * تسجيل عملية إنشاء
     */
    public function logCreate(Model $model, ?string $description = null): void
    {
        $this->log('create', $model, null, $model->attributesToArray(), $description);
    }

    /**
     * تسجيل عملية تحديث — يحسب الحقول المتغيرة فقط
     */
    public function logUpdate(Model $model, array $oldValues, ?string $description = null): void
    {
        $newValues = $model->attributesToArray();
        $changed = [];
        foreach ($newValues as $key => $value) {
            if (!array_key_exists($key, $oldValues) || $oldValues[$key] != $value) {
                $changed[$key] = [
                    'old' => $oldValues[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        if (empty($changed) && !$description) {
            return;
        }

        $this->log('update', $model, $oldValues, $changed, $description);
    }

    /**
     * تسجيل عملية حذف
     */
    public function logDelete(Model $model, ?string $description = null): void
    {
        $this->log('delete', $model, $model->attributesToArray(), null, $description);
    }

    /**
     * تسجيل تسجيل الدخول
     */
    public function logLogin(?int $userId = null): void
    {
        AuditLog::create([
            'user_id'        => $userId ?? Auth::id(),
            'action'         => 'login',
            'auditable_type' => 'App\\Models\\User',
            'auditable_id'   => $userId ?? Auth::id(),
            'new_values'     => ['description' => 'تسجيل دخول ناجح'],
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * تسجيل تسجيل الخروج
     */
    public function logLogout(?int $userId = null): void
    {
        AuditLog::create([
            'user_id'        => $userId ?? Auth::id(),
            'action'         => 'logout',
            'auditable_type' => 'App\\Models\\User',
            'auditable_id'   => $userId ?? Auth::id(),
            'new_values'     => ['description' => 'تسجيل خروج'],
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * تسجيل فشل تسجيل الدخول
     */
    public function logLoginFailed(string $email): void
    {
        AuditLog::create([
            'user_id'        => null,
            'action'         => 'login_failed',
            'auditable_type' => 'App\\Models\\User',
            'auditable_id'   => 0,
            'new_values'     => ['email' => $email, 'description' => 'محاولة دخول فاشلة'],
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * تسجيل عملية حساسة (confirm/cancel للسندات)
     */
    public function logSensitive(string $action, Model $model, ?string $description = null): void
    {
        $this->log($action, $model, null, $model->attributesToArray(), $description);
    }

    /**
     * قاموس أسماء الإجراءات للعرض
     */
    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'create'            => 'إنشاء',
            'update'            => 'تحديث',
            'delete'            => 'حذف',
            'login'             => 'تسجيل دخول',
            'logout'            => 'تسجيل خروج',
            'login_failed'      => 'محاولة دخول فاشلة',
            'receipt_confirmed' => 'تأكيد سند إدخال',
            'receipt_cancelled' => 'إلغاء سند إدخال',
            'issue_confirmed'   => 'تأكيد سند صرف',
            'issue_cancelled'   => 'إلغاء سند صرف',
            'transfer_sent'     => 'إرسال تحويل',
            'transfer_received' => 'استلام تحويل',
            'transfer_cancelled'=> 'إلغاء تحويل',
            'stock_balance_update' => 'تحديث رصيد مخزون',
            default             => $action,
        };
    }

    /**
     * قاموس أسماء النماذج للعرض
     */
    public static function modelLabel(string $modelClass): string
    {
        return match (class_basename($modelClass)) {
            'User'             => 'مستخدم',
            'Role'             => 'دور',
            'Warehouse'        => 'مخزن',
            'Category'         => 'تصنيف',
            'Unit'             => 'وحدة قياس',
            'Item'             => 'صنف',
            'StockReceipt'     => 'سند إدخال',
            'StockIssue'       => 'سند صرف',
            'StockTransfer'    => 'تحويل',
            'StockBalance'     => 'رصيد مخزون',
            'StockTransaction' => 'حركة مخزون',
            default            => class_basename($modelClass),
        };
    }

    /**
     * لون Badge للإجراء
     */
    public static function actionBadge(string $action): string
    {
        return match ($action) {
            'create'            => 'badge-soft-success',
            'update'            => 'badge-soft-primary',
            'delete'            => 'badge-soft-danger',
            'login'             => 'badge-soft-success',
            'logout'            => 'badge-soft-secondary',
            'login_failed'      => 'badge-soft-danger',
            'receipt_confirmed', 'issue_confirmed', 'transfer_received' => 'badge-soft-success',
            'receipt_cancelled', 'issue_cancelled', 'transfer_cancelled' => 'badge-soft-warning',
            'transfer_sent'     => 'badge-soft-info',
            'stock_balance_update' => 'badge-soft-primary',
            default             => 'badge-soft-secondary',
        };
    }
}