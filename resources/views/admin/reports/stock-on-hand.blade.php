@extends('layouts.app')

@section('title', 'تقرير المخزون الحالي')
@section('page-title', 'تقرير المخزون الحالي')
@section('page-subtitle', 'الأرصدة الحالية للأصناف حسب المخزن')

@section('content')
<div class="table-card">
    <div class="card-header">
        <i class="bi bi-box-seam me-2"></i>المخزون الحالي
    </div>

    <form method="GET" class="p-3 border-bottom">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="warehouse_id" class="form-select form-select-sm">
                    <option value="">كل المخازن</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">كل التصنيفات</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="بحث بالصنف أو الكود..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i> تصفية</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>الصنف</th>
                    <th>التصنيف</th>
                    <th>الوحدة</th>
                    <th>الكمية الحالية</th>
                    <th>الحد الأدنى</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><code>{{ $item->code }}</code></td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td>{{ $item->unit->name ?? '-' }}</td>
                    <td><strong>{{ number_format($item->current_quantity ?? 0, 2) }}</strong></td>
                    <td>{{ number_format($item->min_stock ?? 0, 2) }}</td>
                    <td>
                        @if(($item->current_quantity ?? 0) <= ($item->min_stock ?? 0))
                            <span class="badge badge-soft-danger">تحت الحد الأدنى</span>
                        @else
                            <span class="badge badge-soft-success">متوفر</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
    <div class="card-footer bg-white">{{ $items->links() }}</div>
    @endif
</div>
@endsection