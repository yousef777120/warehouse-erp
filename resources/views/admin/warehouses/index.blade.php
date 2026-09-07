@extends('layouts.app')
@section('title', 'المخازن')
@section('page-title', 'المخازن')
@section('page-subtitle', 'إدارة مخازن النظام')

@section('content')
<div class="table-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-building-fill me-2"></i>قائمة المخازن</span>
        @can('manage_warehouses')
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة مخزن
        </a>
        @endcan
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.warehouses.index') }}" class="p-3 bg-light border-bottom">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="بحث بالاسم / الكود / الموقع / المدير" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">كل الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطّل</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> بحث</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-sm btn-outline-secondary w-100">إعادة تعيين</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الكود</th>
                    <th>الاسم</th>
                    <th>الموقع</th>
                    <th>المسؤول</th>
                    <th>الهاتف</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouses as $wh)
                <tr>
                    <td>{{ $wh->id }}</td>
                    <td><code>{{ $wh->code }}</code></td>
                    <td><strong>{{ $wh->name }}</strong></td>
                    <td>{{ $wh->location ?? '-' }}</td>
                    <td>{{ $wh->manager_name ?? '-' }}</td>
                    <td>{{ $wh->phone ?? '-' }}</td>
                    <td>
                        @if($wh->is_active)
                            <span class="badge badge-soft-success"><i class="bi bi-check-circle"></i> نشط</span>
                        @else
                            <span class="badge badge-soft-danger"><i class="bi bi-x-circle"></i> معطّل</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @can('manage_warehouses')
                        <a href="{{ route('admin.warehouses.edit', $wh) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.warehouses.destroy', $wh) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">لا توجد مخازن</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($warehouses->hasPages())
    <div class="card-footer bg-white">{{ $warehouses->links() }}</div>
    @endif
</div>
@endsection