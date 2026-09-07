@extends('layouts.app')

@section('title', 'ملخص حركة المخزون')
@section('page-title', 'ملخص حركة المخزون')
@section('page-subtitle', 'تقرير الوارد والصادر خلال فترة محددة')

@section('content')
<div class="container-fluid">
    
    {{-- فلاتر البحث --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.movement-summary') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from_date" class="form-control" 
                               value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control" 
                               value="{{ $toDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المخزن</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">كل المخازن</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" 
                                        {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع الحركة</label>
                        <select name="movement" class="form-select">
                            <option value="">الكل</option>
                            <option value="in" {{ request('movement') == 'in' ? 'selected' : '' }}>وارد</option>
                            <option value="out" {{ request('movement') == 'out' ? 'selected' : '' }}>صادر</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- بطاقات الإحصائيات --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">إجمالي الوارد</h6>
                            <h3 class="mb-0">{{ number_format($totalIn, 2) }}</h3>
                            <small>وحدة</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-arrow-down-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">إجمالي الصادر</h6>
                            <h3 class="mb-0">{{ number_format($totalOut, 2) }}</h3>
                            <small>وحدة</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-arrow-up-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">عدد الحركات</h6>
                            <h3 class="mb-0">{{ $totalTransactions }}</h3>
                            <small>عملية</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-activity"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ملخص حسب المخزن --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-building me-2"></i>
                الحركة حسب المخزن
            </h5>
        </div>
        <div class="card-body">
            @if($summaryByWarehouse->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>المخزن</th>
                                <th class="text-success">الوارد</th>
                                <th class="text-danger">الصادر</th>
                                <th>الصافي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaryByWarehouse as $row)
                                <tr>
                                    <td>
                                        <strong>{{ $row->warehouse->name ?? 'غير محدد' }}</strong>
                                    </td>
                                    <td class="text-success">
                                        <i class="bi bi-arrow-down-circle me-1"></i>
                                        {{ number_format($row->total_in, 2) }}
                                    </td>
                                    <td class="text-danger">
                                        <i class="bi bi-arrow-up-circle me-1"></i>
                                        {{ number_format($row->total_out, 2) }}
                                    </td>
                                    <td>
                                        @php $net = $row->total_in - $row->total_out; @endphp
                                        <span class="{{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($net, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center mb-0">لا توجد حركات في هذه الفترة</p>
            @endif
        </div>
    </div>

    {{-- ملخص حسب نوع الحركة --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>
                الحركة حسب النوع
            </h5>
        </div>
        <div class="card-body">
            @if($summaryByType->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>نوع الحركة</th>
                                <th>الاتجاه</th>
                                <th>عدد العمليات</th>
                                <th>إجمالي الكمية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaryByType as $row)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $row->transaction_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($row->movement === 'in')
                                            <span class="badge bg-success">وارد</span>
                                        @else
                                            <span class="badge bg-danger">صادر</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->operations_count }}</td>
                                    <td>
                                        <strong>{{ number_format($row->total_quantity, 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center mb-0">لا توجد حركات في هذه الفترة</p>
            @endif
        </div>
    </div>

    {{-- آخر الحركات --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-clock-history me-2"></i>
                آخر 20 حركة
            </h5>
        </div>
        <div class="card-body">
            @if($recentTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>الصنف</th>
                                <th>المخزن</th>
                                <th>النوع</th>
                                <th>الاتجاه</th>
                                <th>الكمية</th>
                                <th>المستخدم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <small>{{ $transaction->created_at->format('Y-m-d H:i') }}</small>
                                    </td>
                                    <td>
                                        {{ $transaction->item->name ?? '-' }}
                                        @if($transaction->item && $transaction->item->unit)
                                            <small class="text-muted">({{ $transaction->item->unit->name }})</small>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->warehouse->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $transaction->transaction_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->movement === 'in')
                                            <span class="badge bg-success">وارد</span>
                                        @else
                                            <span class="badge bg-danger">صادر</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($transaction->quantity, 2) }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $transaction->user->name ?? '-' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center mb-0">لا توجد حركات في هذه الفترة</p>
            @endif
        </div>
    </div>

</div>
@endsection