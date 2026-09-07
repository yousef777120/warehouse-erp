@extends('layouts.print')

@section('title', 'سند إدخال رقم ' . $receipt->serial)

@section('content')
<div class="print-toolbar">
    <button onclick="window.print()" class="btn btn-print">🖨️ طباعة</button>
    <button onclick="window.close()" class="btn">إغلاق</button>
</div>

<div class="sheet">
    <header class="sheet-header">
        <div>
            <h4>{{ config('app.name') }}</h4>
            <small>نظام إدارة المخازن</small>
        </div>
        <div class="doc-title">
            <h5>سند إدخال مخزن</h5>
            <span>رقم: <strong>{{ $receipt->serial }}</strong></span>
        </div>
    </header>

    <table class="meta">
        <tr>
            <td>التاريخ: {{ $receipt->receipt_date->format('Y/m/d') }}</td>
            <td>المخزن: {{ $receipt->warehouse->name ?? '-' }}</td>
            <td>المورد: {{ $receipt->supplier_name ?? '-' }}</td>
            <td>الحالة: {{ $receipt->status_label }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th><th>الكود</th><th>الصنف</th><th>الوحدة</th>
                <th>الكمية</th><th>سعر الوحدة</th><th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->item->code ?? '-' }}</td>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td>{{ $item->item->unit->name ?? '-' }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">الإجمالي الكلي</td>
                <td>{{ number_format($receipt->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($receipt->notes)
        <p class="notes"><strong>ملاحظات:</strong> {{ $receipt->notes }}</p>
    @endif

    <div class="signatures">
        <div>إعداد: {{ $receipt->creator->name ?? '-' }}</div>
        <div>استلام: ....................</div>
        <div>اعتماد: ....................</div>
    </div>

    <footer class="sheet-footer">
        طُبع بتاريخ {{ now()->format('Y/m/d H:i') }} — بواسطة {{ auth()->user()->name }}
    </footer>
</div>
@endsection