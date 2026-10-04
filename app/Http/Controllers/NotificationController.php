<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * جلب جميع الإشعارات
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * جلب الإشعارات غير المقروءة (AJAX)
     */
    public function getUnreadNotifications(): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        $count = $notifications->count();

        return response()->json([
            'notifications' => $notifications,
            'count' => $count,
        ]);
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الإشعار كمقروء',
        ]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد جميع الإشعارات كمقروءة',
        ]);
    }

    /**
     * حذف إشعار
     */
    public function destroy($id): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإشعار',
        ]);
    }

    /**
     * إنشاء إشعار جديد (للاختبار)
     */
    public function createTestNotification(Request $request)
    {
        $types = ['info', 'success', 'warning', 'danger'];
        $icons = [
            'info' => 'bi-info-circle',
            'success' => 'bi-check-circle',
            'warning' => 'bi-exclamation-triangle',
            'danger' => 'bi-x-circle',
        ];

        $type = $request->input('type', 'info');

        Notification::create([
            'user_id' => auth()->id(),
            'title' => $request->input('title', 'إشعار جديد'),
            'message' => $request->input('message', 'هذا إشعار تجريبي'),
            'type' => $type,
            'icon' => $icons[$type],
            'link' => $request->input('link', '#'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الإشعار',
        ]);
    }
}