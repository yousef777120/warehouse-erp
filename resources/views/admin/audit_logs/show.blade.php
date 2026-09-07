@extends('layouts.app')
@section('title', 'تفاصيل العملية')
@section('page-title', 'تفاصيل العملية')
@section('page-subtitle', 'تفاصيل العملية #' . $log->id)

@section('content')
<div class="table-card mb-3">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>تفاصيل العملية</div>
    <div class="p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <strong>التاريخ:</strong> {{ $log->created_at->format('Y-m-d H:i:s') }}
            </div>
            <div class="col-md-6">
                <strong>المستخدم:</strong> {{ $log->user->name ?? 'غير معروف' }}
            </div>
            <div class="col-md-6">
                <strong>العملية:</strong> {{ $log->action }}
            </div>
            <div class="col-md-6">
                <strong>النوع:</strong> {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
            </div>
        </div>

        @if($log->old_values)
        <div class="mt-4">
            <h6 class="fw-bold">القيم القديمة:</h6>
            <pre class="bg-light p-3 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif

        @if($log->new_values)
        <div class="mt-3">
            <h6 class="fw-bold">القيم الجديدة:</h6>
            <pre class="bg-light p-3 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
    </div>
</div>
@endsection