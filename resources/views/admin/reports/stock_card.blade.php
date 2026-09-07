@extends('layouts.app')
@section('title', 'بطاقة الصنف')
@section('page-title', 'بطاقة الصنف')
@section('page-subtitle', 'حركة صنف معين خلال فترة')

@section('content')
<div class="table-card mb-3">
    <div class="card-header"><i class="bi bi-card-list me-2"></i>بطاقة الصنف</div>

    <form method="GET" class="p-3 border-bottom">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="item_id" class="form-select form-select-sm" required>
                    <option value="">اختر الصنف</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->code }} - {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="warehouse_id" class="form-select form-select-sm">
                    <option value="">كل المخازن</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">عرض</button>
            </div>
        </div>
    </form>

    @if($item)
    <div class="p-3 border-bottom bg-light">
        <div class="row">
            <div class="col-md-3"><strong>الصنف:</strong> {{ $item->name }}</div>
            <div class="col-md-3"><strong>الكود:</strong> {{ $item->code }}</div>
            <div class="col-md-3"><strong>الوحدة:</strong> {{ $item->unit->name ?? '-' }}</div>
        </div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>الكمية</th>
                    <th>المخزن</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if($transaction->movement === 'in')
                            <span class="badge badge-soft-success">إدخال</span>
                        @else
                            <span class="badge badge-soft-danger">صرف</span>
                        @endif
                    </td>
                    <td><strong>{{ number_format($transaction->quantity, 2) }}</strong></td>
                    <td>{{ $transaction->warehouse->name ?? '-' }}</td>
                    <td>{{ $transaction->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد حركات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-white">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection