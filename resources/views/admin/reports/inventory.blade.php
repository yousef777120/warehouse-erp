@extends('layouts.app')

@section('title', 'تقرير المخزون')

@section('content')

{{-- رأس الطباعة --}}
<x-print-header documentTitle="تقرير حالة المخزون" />

{{-- أزرار الطباعة --}}
<x-print-actions />

{{-- محتوى التقرير --}}
<div class="card mb-4">
    <div class="card-body">
        <h4 class="mb-3">ملخص المخزون</h4>
        <div class="row">
            <div class="col-md-3">
                <div class="info-card text-center">
                    <h3>{{ $totalItems }}</h3>
                    <p class="text-muted">إجمالي الأصناف</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card text-center">
                    <h3>{{ $totalQuantity }}</h3>
                    <p class="text-muted">إجمالي الكمية</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card text-center">
                    <h3>{{ $lowStockItems }}</h3>
                    <p class="text-warning">مخزون منخفض</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card text-center">
                    <h3>{{ $outOfStockItems }}</h3>
                    <p class="text-danger">نفذ من المخزون</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- جدول التفاصيل --}}
<div class="card">
    <div class="card-body">
        <h4 class="mb-3">تفاصيل الأصناف</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>كود الصنف</th>
                    <th>اسم الصنف</th>
                    <th>التصنيف</th>
                    <th>المخزن</th>
                    <th>الكمية</th>
                    <th>الحد الأدنى</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td>{{ $item->warehouse->name }}</td>
                    <td>{{ number_format($item->quantity, 3) }}</td>
                    <td>{{ number_format($item->min_stock, 3) }}</td>
                    <td>
                        @if($item->quantity == 0)
                            <span class="badge bg-danger">نفذ</span>
                        @elseif($item->quantity <= $item->min_stock)
                            <span class="badge bg-warning">منخفض</span>
                        @else
                            <span class="badge bg-success">متوفر</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection