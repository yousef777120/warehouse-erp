@extends('layouts.app')

@section('title', 'سجل العمليات')
@section('page-title', 'سجل العمليات')
@section('page-subtitle', 'تتبع جميع العمليات على النظام')

@section('content')
<div class="table-card">
    <div class="card-header">
        <i class="bi bi-clock-history me-2"></i>سجل العمليات
    </div>

    {{-- فلاتر البحث --}}
    <div class="p-3 border-bottom">
        <form method="GET">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="بحث عام..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">كل المستخدمين</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="action" class="form-select form-select-sm">
                        <option value="">كل العمليات</option>
                        @foreach($actions as $key => $label)
                            <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="model_type" class="form-select form-select-sm">
                        <option value="">كل النماذج</option>
                        @foreach($modelTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('model_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>بحث
                    </button>
                    <a href="{{ route('admin.audit_logs.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- الجدول --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>العملية</th>
                    <th>النموذج</th>
                    <th>معرف السجل</th>
                    <th>التاريخ</th>
                    <th>عنوان IP</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>
                        @if($log->user)
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    {{ mb_substr($log->user->name, 0, 1) }}
                                </div>
                                <span>{{ $log->user->name }}</span>
                            </div>
                        @else
                            <span class="text-muted">النظام</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $actionBadge = match($log->action) {
                                'created' => 'bg-success',
                                'updated' => 'bg-warning',
                                'deleted' => 'bg-danger',
                                'login' => 'bg-info',
                                'logout' => 'bg-secondary',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $actionBadge }}">
                            {{ $actions[$log->action] ?? $log->action }}
                        </span>
                    </td>
                    <td>{{ $modelTypes[$log->auditable_type] ?? class_basename($log->auditable_type) }}</td>
                    <td>#{{ $log->auditable_id }}</td>
                    <td>
                        <div>{{ $log->created_at->format('Y-m-d') }}</div>
                        <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                    </td>
                    <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                    <td class="text-center">
                        <a href="{{ route('admin.audit_logs.show', $log) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        لا توجد عمليات مسجلة
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="card-footer bg-white">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection