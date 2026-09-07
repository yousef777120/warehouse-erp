@extends('layouts.app')

@section('title', 'الأصناف تحت الحد الأدنى')
@section('page-title', 'الأصناف تحت الحد الأدنى')
@section('page-subtitle', 'الأصناف التي تحتاج إعادة طلب')

@section('content')
{{-- الفلاتر --}}
<div class="table-card mb-4">
    <div class="p-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
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
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search me-1"></i>بحث
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.reports.export.low-stock', request()->query()) }}" class="btn btn-sm btn-success w-100">
                    <i class="bi bi-download me-1"></i>تصدير
                </a>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>الصنف</th>
                    <th>المخزن</th>
                    <th class="text-end">الكمية الحالية</th>
                    <th class="text-end">الحد الأدنى</th>
                    <th class="text-end">العجز</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStockItems as $balance)
                @php $deficit = (float) $balance->item->min_stock - (float) $balance->quantity; @endphp
                <tr>
                    <td><code>{{ $balance->item->code }}</code></td>
                    <td class="fw-semibold">{{ $balance->item->name }}</td>
                    <td>{{ $balance->warehouse->name }}</td>
                    <td class="text-end fw-bold text-danger">{{ number_format($balance->quantity, 3) }}</td>
                    <td class="text-end">{{ number_format($balance->item->min_stock, 3) }}</td>
                    <td class="text-end text-danger fw-bold">{{ number_format($deficit, 3) }}</td>
                    <td>
                        @if($balance->quantity == 0)
                            <span class="badge badge-soft-danger">نفد المخزون</span>
                        @else
                            <span class="badge badge-soft-warning">تحت الحد الأدنى</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-check-circle text-success me-2"></i>لا توجد أصناف تحت الحد الأدنى
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($lowStockItems->hasPages())
    <div class="card-footer bg-white">{{ $lowStockItems->links() }}</div>
    @endif
</div>
@endsection