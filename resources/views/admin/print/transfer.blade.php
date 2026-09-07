@extends('layouts.print')

@section('title', 'تحويل رقم ' . $transfer->serial)

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
            <h5>سند تحويل بين المخازن</h5>
            <span>رقم: <strong>{{ $transfer->serial }}</strong></span>
        </div>
    </header>

    <table class="meta">
        <tr>
            <td>التاريخ: {{ $transfer->transfer_date->format('Y/m/d') }}</td>
            <td>من مخزن: {{ $transfer->fromWarehouse->name ?? '-' }}</td>
            <td>إلى مخزن: {{ $transfer->toWarehouse->name ?? '-' }}</td>
            <td>الحالة: {{ $transfer->status_label }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th><th>الكود</th><th>الصنف</th><th>الوحدة</th><th>الكمية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfer->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->item->code ?? '-' }}</td>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td>{{ $item->item->unit->name ?? '-' }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($transfer->notes)
        <p class="notes"><strong>ملاحظات:</strong> {{ $transfer->notes }}</p>
    @endif

    <div class="signatures">
        <div>إعداد: {{ $transfer->creator->name ?? '-' }}</div>
        <div>مُرسِل: ....................</div>
        <div>مُستلِم: ....................</div>
    </div>

    <footer class="sheet-footer">
        طُبع بتاريخ {{ now()->format('Y/m/d H:i') }} — بواسطة {{ auth()->user()->name }}
    </footer>
</div>
@endsection