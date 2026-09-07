@extends('layouts.app')
@section('title', 'التحويلات بين المخازن')
@section('page-title', 'التحويلات بين المخازن')
@section('page-subtitle', 'إدارة التحويلات بين المخازن')

@section('content')
<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-arrow-left-right me-2"></i>قائمة التحويلات</span>
        @can('manage_transfers')
        <a href="{{ route('admin.transfers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إنشاء تحويل جديد
        </a>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>الرقم التسلسلي</th>
                    <th>التاريخ</th>
                    <th>من مخزن</th>
                    <th>إلى مخزن</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                <tr>
                    <td><code>{{ $transfer->serial }}</code></td>
                    <td>{{ $transfer->transfer_date->format('Y-m-d') }}</td>
                    <td>{{ $transfer->fromWarehouse->name ?? '-' }}</td>
                    <td>{{ $transfer->toWarehouse->name ?? '-' }}</td>
                    <td>
                        @switch($transfer->status)
                            @case('draft')
                                <span class="badge badge-soft-warning">مسودة</span>
                                @break
                            @case('in_transit')
                                <span class="badge badge-soft-primary">قيد النقل</span>
                                @break
                            @case('received')
                                <span class="badge badge-soft-success">مستلم</span>
                                @break
                            @case('cancelled')
                                <span class="badge badge-soft-danger">ملغي</span>
                                @break
                        @endswitch
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> عرض
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">لا توجد تحويلات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transfers->hasPages())
    <div class="card-footer bg-white">{{ $transfers->links() }}</div>
    @endif
</div>
@endsection