@extends('layouts.app')
@section('title', 'سندات الصرف')
@section('page-title', 'سندات الصرف')
@section('page-subtitle', 'إدارة سندات صرف الأصناف من المخازن')

@section('content')
<div class="table-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-box-arrow-up-right me-2"></i>قائمة سندات الصرف</span>
        @can('manage_issues')
        <a href="{{ route('admin.issues.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> سند صرف جديد
        </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('admin.issues.index') }}" class="p-3 bg-light border-bottom">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="بحث بالرقم / المستلم / المرجع" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="warehouse_id" class="form-select form-select-sm">
                    <option value="">كل المخازن</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">كل الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>الرقم التسلسلي</th>
                    <th>التاريخ</th>
                    <th>المخزن</th>
                    <th>المستلم</th>
                    <th>عدد الأصناف</th>
                    <th>إجمالي الكمية</th>
                    <th>الحالة</th>
                    <th>بواسطة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($issues as $i)
                <tr>
                    <td><code class="fw-bold">{{ $i->serial }}</code></td>
                    <td>{{ $i->issue_date->format('Y-m-d') }}</td>
                    <td>{{ $i->warehouse->name }}</td>
                    <td>{{ $i->recipient_name ?? '-' }}</td>
                    <td><span class="badge badge-soft-primary">{{ $i->items->count() }}</span></td>
                    <td><strong>{{ number_format($i->total_quantity, 3) }}</strong></td>
                    <td><span class="badge {{ $i->status_badge }}"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> {{ $i->status_label }}</span></td>
                    <td><small>{{ $i->creator->name ?? '-' }}</small></td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('admin.issues.show', $i) }}" class="btn btn-sm btn-outline-secondary" title="عرض"><i class="bi bi-eye"></i></a>
                        @can('manage_issues')
                            @if($i->isDraft)
                                <a href="{{ route('admin.issues.edit', $i) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.issues.confirm', $i) }}" method="POST" class="d-inline" onsubmit="return confirm('تأكيد السند سيخصم الأصناف من المخزون. هل أنت متأكد؟')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="تأكيد"><i class="bi bi-check-circle"></i></button>
                                </form>
                                <form action="{{ route('admin.issues.destroy', $i) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                </form>
                            @elseif($i->isConfirmed)
                                <form action="{{ route('admin.issues.cancel', $i) }}" method="POST" class="d-inline" onsubmit="return confirm('إلغاء السند سيعيد الأصناف للمخزون. هل أنت متأكد؟')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="إلغاء"><i class="bi bi-x-circle"></i></button>
                                </form>
                            @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">لا توجد سندات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($issues->hasPages())
    <div class="card-footer bg-white">{{ $issues->links() }}</div>
    @endif
</div>
@endsection