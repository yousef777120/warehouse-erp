<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuditLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_audit_logs'),
        ];
    }

    public function index(Request $request)
    {
        $query = AuditLog::with(['user'])->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('auditable_type', 'like', '%' . $request->model_type . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        $actions = [
            'created' => 'إنشاء',
            'updated' => 'تحديث',
            'deleted' => 'حذف',
        ];

        $modelTypes = [
            'App\\Models\\User'          => 'مستخدم',
            'App\\Models\\Warehouse'     => 'مخزن',
            'App\\Models\\Category'      => 'تصنيف',
            'App\\Models\\Unit'          => 'وحدة قياس',
            'App\\Models\\Item'          => 'صنف',
            'App\\Models\\StockReceipt'  => 'سند إدخال',
            'App\\Models\\StockIssue'    => 'سند صرف',
            'App\\Models\\StockTransfer' => 'تحويل',
        ];

        return view('admin.audit-logs.index', compact('logs', 'users', 'actions', 'modelTypes'));
    }

    public function show(AuditLog $log)
    {
        $log->load(['user']);
        return view('admin.audit-logs.show', compact('log'));
    }
}