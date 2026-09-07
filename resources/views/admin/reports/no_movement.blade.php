@extends('layouts.app')
@section('title', 'الأصناف الراكدة')
@section('page-title', 'الأصناف الراكدة')
@section('page-subtitle', 'الأصناف التي لم تتحرك لفترة طويلة')

@section('content')
<div class="table-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-hourglass-split me-2"></i>الأصناف الراكدة ({{ $rows->count() }})</span>
        @can('export_reports')
        <a href="{{ route('admin.reports.no_movement', array_merge(request()->all(), ['export' => 1])) }}" class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> تصدير CSV
        </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('admin.reports.no_movement') }}" class="p-3 bg-light border-bottom">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="warehouse_id" class="form-select form-select-sm">
                    <option value="">كل المخازن</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="days" class="form-select form-select-sm">
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>آخر 30 يوم</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>آخر 60 يوم</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>آخر 90 يوم</option>
                    <option value="180" {{ $days == 180 ? 'selected' : '' }}>آخر 180 يوم</option>
                    <option value="365" {{ $days == 365 ? 'selected' : '' }}>آخر سنة</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> بحث</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.reports.no_movement') }}" class="btn btn-sm btn-outline-secondary w-100">إعادة</a>
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
                    <th>المخزن</th>
                    <th>الوحدة</th>
                    <th class="text-end">الرصيد الراكد</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td><code>{{ $row->item->code }}</code></td>
                    <td><strong>{{ $row->item->name }}</strong></td>
                    <td>{{ $row->item->category->name ?? '-' }}</td>
                    <td>{{ $row->warehouse->name }}</td>
                    <td>{{ $row->item->unit->name ?? '-' }}</td>
                    <td class="text-end"><strong class="text-warning">{{ number_format($row->quantity, 3) }}</strong></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i>
                    لا توجد أصناف راكدة في الفترة المحددة
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        <small class="text-muted">الفترة: آخر {{ $days }} يوم — تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</small>
    </div>
</div>
@endsection