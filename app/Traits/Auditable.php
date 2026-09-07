<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->writeAudit('created', null, $model->maskSensitive($model->attributesToArray()));
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            unset($dirty['updated_at'], $dirty['created_at']);

            if (empty($dirty)) {
                return;
            }

            $old = [];
            foreach (array_keys($dirty) as $key) {
                $old[$key] = $model->getOriginal($key);
            }

            $model->writeAudit(
                'updated',
                $model->maskSensitive($old),
                $model->maskSensitive($dirty)
            );
        });

        static::deleted(function ($model) {
            $model->writeAudit('deleted', $model->maskSensitive($model->attributesToArray()), null);
        });
    }

    protected function writeAudit(string $action, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => $this->getMorphClass(),
            'auditable_id'   => $this->getKey(),
            'old_values'     => $old,
            'new_values'     => $new,
            'ip_address'     => request()?->ip(),
            'user_agent'     => request()?->userAgent(),
        ]);
    }

    /** إخفاء الحقول الحساسة */
    protected function maskSensitive(array $values): array
    {
        foreach (['password', 'remember_token'] as $field) {
            if (array_key_exists($field, $values)) {
                $values[$field] = '***';
            }
        }

        return $values;
    }
}