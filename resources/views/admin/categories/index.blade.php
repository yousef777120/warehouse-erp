@extends('layouts.app')

@section('title', 'التصنيفات')
@section('page-title', 'التصنيفات')
@section('page-subtitle', 'إدارة تصنيفات الأصناف')

@section('content')
<div class="table-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-tags-fill me-2"></i>قائمة التصنيفات</span>
        @can('manage_categories')
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة تصنيف
        </a>
        @endcan
    </div>

    {{-- فلاتر البحث --}}
    <form method="GET" class="p-3 bg-light border-bottom">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="بحث بالاسم أو الكود..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="parent_id" class="form-select form-select-sm">
                    <option value="">كل التصنيفات</option>
                    <option value="0" {{ request('parent_id') === '0' ? 'selected' : '' }}>رئيسية فقط</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ request('parent_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search"></i> بحث
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                    إعادة تعيين
                </a>
            </div>
        </div>
    </form>

    {{-- الجدول --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الكود</th>
                    <th>الاسم</th>
                    <th>التصنيف الأب</th>
                    <th>عدد الفروع</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td><code>{{ $category->code }}</code></td>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td>
                        @if($category->parent)
                            <span class="badge badge-soft-primary">{{ $category->parent->name }}</span>
                        @else
                            <span class="badge badge-soft-warning">رئيسي</span>
                        @endif
                    </td>
                    <td><span class="badge badge-soft-primary">{{ $category->children_count }}</span></td>
                    <td>
                        @if($category->is_active)
                            <span class="badge badge-soft-success">نشط</span>
                        @else
                            <span class="badge badge-soft-danger">معطّل</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @can('manage_categories')
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">لا توجد تصنيفات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
    <div class="card-footer bg-white">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection