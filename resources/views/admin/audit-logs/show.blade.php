@extends('layouts.app')

@section('title', 'تفاصيل العملية')
@section('page-title', 'تفاصيل العملية')
@section('page-subtitle', 'عرض تفاصيل العملية #' . $log->id)

@section('content')
<div class="row g-4">
    {{-- معلومات العملية --}}
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>تفاصيل العملية
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small">المستخدم</label>
                            <div class="fw-bold">
                                @if($log->user)
                                    {{ $log->user->name }}
                                @else
                                    <span class="text-muted">النظام</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">العملية</label>
                            <div>
                                @php
                                    $actionLabel = match($log->action) {
                                        'created' => 'إنشاء',
                                        'updated' => 'تحديث',
                                        'deleted' => 'حذف',
                                        'login' => 'تسجيل دخول',
                                        'logout' => 'تسجيل خروج',
                                        default => $log->action,
                                    };
                                @endphp
                                <span class="badge bg-primary">{{ $actionLabel }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">النموذج المتأثر</label>
                            <div class="fw-bold">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small">تاريخ العملية</label>
                            <div class="fw-bold">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">عنوان IP</label>
                            <div><code>{{ $log->ip_address ?? '-' }}</code></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">متصفح المستخدم</label>
                            <div class="small text-muted">{{ $log->user_agent ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- القيم القديمة والجديدة --}}
    <div class="col-lg-4">
        @if($log->old_values)
        <div class="table-card mb-3">
            <div class="card-header bg-danger bg-opacity-10">
                <i class="bi bi-x-circle me-2 text-danger"></i>القيم القديمة
            </div>
            <div class="p-3">
                <pre class="mb-0 small" style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        @if($log->new_values)
        <div class="table-card">
            <div class="card-header bg-success bg-opacity-10">
                <i class="bi bi-check-circle me-2 text-success"></i>القيم الجديدة
            </div>
            <div class="p-3">
                <pre class="mb-0 small" style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        @if(!$log->old_values && !$log->new_values)
        <div class="table-card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>ملاحظة
            </div>
            <div class="p-4 text-center text-muted">
                لا توجد قيم مسجلة لهذه العملية
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.audit_logs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i>العودة إلى سجل العمليات
    </a>
</div>
@endsection