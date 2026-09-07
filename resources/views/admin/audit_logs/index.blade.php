@extends('layouts.app')
@section('title', 'سجل العمليات')
@section('page-title', 'سجل العمليات')
@section('page-subtitle', 'تتبع جميع العمليات على النظام')

@section('content')
<div class="table-card mb-3">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>سجل العمليات</div>

    <form method="GET" class="p-3 border-bottom">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="action" class="form-select form-select-sm">
                    <option value="">كل العمليات</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>إنشاء</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>تحديث</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>حذف</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">بحث</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>المستخدم</th>
                    <th>العملية</th>
                    <th>النوع</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $log->user->name ?? '-' }}</td>
                    <td>
                        @php
                            $actionLabels = ['create' => 'إنشاء', 'update' => 'تحديث', 'delete' => 'حذف'];
                        @endphp
                        {{ $actionLabels[$log->action] ?? $log->action }}
                    </td>
                    <td>{{ class_basename($log->auditable_type) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">لا توجد عمليات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>
@endsection