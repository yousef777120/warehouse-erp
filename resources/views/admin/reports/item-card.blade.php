@extends('layouts.app')

@section('title', 'بطاقة الصنف')
@section('page-title', 'بطاقة الصنف')
@section('page-subtitle', 'تتبع حركة صنف معين')

@section('content')
<div class="table-card mb-4">
    <div class="card-header"><i class="bi bi-card-list me-2"></i>بطاقة الصنف</div>
    <form method="GET" class="p-3">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="item_id" class="form-select form-select-sm">
                    <option value="">-- اختر الصنف --</option>
                    @foreach($items as $i)
                        <option value="{{ $i->id }}" @selected(request('item_id') == $i->id)>
                            {{ $i->code }} - {{ $i->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="warehouse_id" class="form-select form-select-sm">
                    <option value="">كل المخازن</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </form>
</div>

@if($item)
<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ $item->code }} - {{ $item->name }}</span>
        <span class="badge badge-soft-primary">الوحدة: {{ $item->unit->name ?? '-' }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>نوع الحركة</th>
                    <th>المخزن</th>
                    <th>الاتجاه</th>
                    <th>الكمية</th>
                    <th>الرصيد بعد الحركة</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $t->transaction_type }}</td>
                    <td>{{ $t->warehouse->name ?? '-' }}</td>
                    <td>
                        @if($t->movement === 'in')
                            <span class="badge badge-soft-success">وارد</span>
                        @else
                            <span class="badge badge-soft-danger">صادر</span>
                        @endif
                    </td>
                    <td>{{ number_format($t->quantity, 3) }}</td>
                    <td><strong>{{ number_format($t->running_balance ?? 0, 3) }}</strong></td>
                    <td>{{ $t->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">لا توجد حركات لهذا الصنف</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection