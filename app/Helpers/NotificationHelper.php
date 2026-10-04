<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function create($userId, $title, $message, $type = 'info', $link = null)
    {
        $icons = [
            'info' => 'bi-info-circle',
            'success' => 'bi-check-circle',
            'warning' => 'bi-exclamation-triangle',
            'danger' => 'bi-x-circle',
        ];

        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icons[$type] ?? 'bi-bell',
            'link' => $link,
        ]);
    }

    public static function lowStock($item, $quantity)
    {
        return self::create(
            auth()->id(),
            'مخزون منخفض',
            "الصنف '{$item->name}' وصل للحد الأدنى (الكمية: {$quantity})",
            'warning',
            route('admin.items.index')
        );
    }

    public static function transferCreated($transfer)
    {
        return self::create(
            auth()->id(),
            'تحويل جديد',
            "تم إنشاء تحويل جديد رقم {$transfer->serial}",
            'info',
            route('admin.transfers.show', $transfer)
        );
    }

    public static function receiptCreated($receipt)
    {
        return self::create(
            auth()->id(),
            'سند إدخال جديد',
            "تم إنشاء سند إدخال رقم {$receipt->serial}",
            'success',
            route('admin.receipts.show', $receipt)
        );
    }
}