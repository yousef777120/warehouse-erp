@extends('layouts.app')

@section('title', 'سندات الإدخال')
@section('page-title', 'سندات الإدخال')
@section('page-subtitle', 'إدارة سندات إدخال البضاعة إلى المخازن')

@section('content')
<div class="table-card">
    {{-- رأس الجدول مع زر الإضافة --}}
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-box-arrow-in-down-left me-2"></i>قائمة سندات الإدخال</span>
        @can('manage_receipts')
        <a href="{{ route('admin.receipts.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> سند إدخال جديد
        </a>
        @endcan
    </div>

    {{-- فلاتر البحث والتصفية --}}
    <div class="p-3 border-bottom bg-light">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="بحث بالرقم أو المورد..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="warehouse_id" class="form-select form-select-sm">
                    <option value="">كل المخازن</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
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
            <div class="col-md-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.receipts.index') }}" class="btn btn-sm btn-outline-secondary w-100" title="إعادة تعيين">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- جدول السندات --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم السند</th>
                    <th>التاريخ</th>
                    <th>المخزن</th>
                    <th>المورد</th>
                    <th class="text-center">عدد الأصناف</th>
                    <th class="text-end">الإجمالي</th>
                    <th class="text-center">الحالة</th>
                    <th>المنشئ</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                <tr>
                    <td>{{ $receipt->id }}</td>
                    <td>
                        <a href="{{ route('admin.receipts.show', $receipt) }}" class="fw-bold text-primary text-decoration-none">
                            {{ $receipt->serial }}
                        </a>
                    </td>
                    <td>{{ $receipt->receipt_date->format('Y/m/d') }}</td>
                    <td>{{ $receipt->warehouse->name ?? '-' }}</td>
                    <td>{{ $receipt->supplier_name ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-soft-primary">{{ $receipt->items_count ?? $receipt->items->count() }}</span>
                    </td>
                    <td class="text-end fw-bold">
                        {{ number_format($receipt->total_amount, 2) }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $receipt->status_badge }}">{{ $receipt->status_label }}</span>
                    </td>
                    <td>{{ $receipt->creator->name ?? '-' }}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            {{-- عرض --}}
                            <a href="{{ route('admin.receipts.show', $receipt) }}" class="btn btn-outline-info" title="عرض">
                                <i class="bi bi-eye"></i>
                            </a>

                            @can('manage_receipts')
                                {{-- تعديل وحذف — فقط للمسودات --}}
                                @if($receipt->is_draft)
                                    <a href="{{ route('admin.receipts.edit', $receipt) }}" class="btn btn-outline-primary" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.receipts.destroy', $receipt) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا السند؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        لا توجد سندات إدخال
                        @can('manage_receipts')
                        <br>
                        <a href="{{ route('admin.receipts.create') }}" class="text-primary">إنشاء أول سند إدخال</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- الترقيم --}}
    @if($receipts->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">
            عرض {{ $receipts->firstItem() }} إلى {{ $receipts->lastItem() }} من أصل {{ $receipts->total() }} سند
        </small>
        {{ $receipts->links() }}
    </div>
    @endif
</div>
@endsection