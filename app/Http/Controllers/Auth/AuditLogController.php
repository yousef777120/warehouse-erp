<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_audit_logs');
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('auditable_type', $request->model_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('action', 'like', "%{$s}%")
                  ->orWhere('auditable_type', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$s}%"));
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $users = User::orderBy('name')->get();
        $actions = $this->getDistinctActions();
        $modelTypes = $this->getDistinctModelTypes();

        // تصدير CSV
        if ($request->has('export')) {
            abort_unless(auth()->user()->can('export_audit_logs'), 403);
            return $this->exportCsv($logs);
        }

        return view('admin.audit-logs.index', compact('logs', 'users', 'actions', 'modelTypes'));
    }

    public function show(AuditLog $log)
    {
        abort_unless(auth()->user()->can('view_audit_logs'), 403);
        $log->load('user');
        return view('admin.audit-logs.show', compact('log'));
    }

    protected function getDistinctActions(): array
    {
        return AuditLog::distinct()->pluck('action')->filter()->values()->toArray();
    }

    protected function getDistinctModelTypes(): array
    {
        return AuditLog::distinct()->pluck('auditable_type')->filter()->values()->toArray();
    }

    protected function exportCsv($logs): StreamedResponse
    {
        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        return new StreamedResponse(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['التاريخ', 'المستخدم', 'الإجراء', 'النموذج', 'المعرف', 'IP', 'التفاصيل']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'نظام',
                    AuditLogService::actionLabel($log->action),
                    AuditLogService::modelLabel($log->auditable_type),
                    $log->auditable_id,
                    $log->ip_address,
                    json_encode($log->new_values ?? $log->old_values, JSON_UNESCAPED_UNICODE),
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}