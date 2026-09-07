@extends('layouts.app')
@section('title', 'الأصناف')
@section('page-title', 'الأصناف')
@section('page-subtitle', 'إدارة أصناف المخزون')

@section('content')
<div class="table-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-box-seam-fill me-2"></i>قائمة الأصناف</span>
        @can('manage_items')
        <a href="{{ route('admin.items.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة صنف
        </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('admin.items.index') }}" class="p-3 bg-light border-bottom">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="بحث بالاسم / الكود / الباركود / SKU" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">كل التصنيفات</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="unit_id" class="form-select form-select-sm">
                    <option value="">كل الوحدات</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> بحث</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-outline-secondary w-100">إعادة تعيين</a>
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
                    <th>الباركود</th>
                    <th>التصنيف</th>
                    <th>الوحدة</th>
                    <th>الحد الأدنى</th>
                    <th>الحد الأقصى</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td><code>{{ $item->code }}</code></td>
                    <td>
                        <strong>{{ $item->name }}</strong>
                        @if($item->sku)
                            <br><small class="text-muted">SKU: {{ $item->sku }}</small>
                        @endif
                    </td>
                    <td><code>{{ $item->barcode ?? '-' }}</code></td>
                    <td>{{ $item->category?->name ?? '-' }}</td>
                    <td><span class="badge badge-soft-primary">{{ $item->unit?->name ?? '-' }}</span></td>
                    <td>{{ number_format($item->min_stock, 3) }}</td>
                    <td>{{ number_format($item->max_stock, 3) }}</td>
                    <td>
                        @if($item->is_active)
                            <span class="badge badge-soft-success"><i class="bi bi-check-circle"></i> نشط</span>
                        @else
                            <span class="badge badge-soft-danger"><i class="bi bi-x-circle"></i> معطّل</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @can('manage_items')
                        <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">لا توجد أصناف</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="card-footer bg-white">{{ $items->links() }}</div>
    @endif
</div>
@endsection